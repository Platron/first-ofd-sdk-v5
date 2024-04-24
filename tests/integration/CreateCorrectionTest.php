<?php

namespace Platron\FirstOfdV5\tests\integration;

use Platron\FirstOfdV5\clients\PostClient;
use Platron\FirstOfdV5\data_objects\AgentInfo;
use Platron\FirstOfdV5\data_objects\Client;
use Platron\FirstOfdV5\data_objects\Company;
use Platron\FirstOfdV5\data_objects\Correction;
use Platron\FirstOfdV5\data_objects\CorrectionInfo;
use Platron\FirstOfdV5\data_objects\Item;
use Platron\FirstOfdV5\data_objects\MarkCode;
use Platron\FirstOfdV5\data_objects\MarkQuantity;
use Platron\FirstOfdV5\data_objects\MoneyTransferOperator;
use Platron\FirstOfdV5\data_objects\PayingAgent;
use Platron\FirstOfdV5\data_objects\Payment;
use Platron\FirstOfdV5\data_objects\ReceivePaymentsOperator;
use Platron\FirstOfdV5\data_objects\SectoralItemProps;
use Platron\FirstOfdV5\data_objects\Supplier;
use Platron\FirstOfdV5\data_objects\Vat;
use Platron\FirstOfdV5\handbooks\AgentTypes;
use Platron\FirstOfdV5\handbooks\CorrectionOperationTypes;
use Platron\FirstOfdV5\handbooks\CorrectionTypes;
use Platron\FirstOfdV5\handbooks\MarkCodeTypes;
use Platron\FirstOfdV5\handbooks\Measures;
use Platron\FirstOfdV5\handbooks\PaymentMethods;
use Platron\FirstOfdV5\handbooks\PaymentObjects;
use Platron\FirstOfdV5\handbooks\PaymentTypes;
use Platron\FirstOfdV5\handbooks\SnoTypes;
use Platron\FirstOfdV5\handbooks\Vates;
use Platron\FirstOfdV5\SdkException;
use Platron\FirstOfdV5\services\CreateCorrectionRequest;
use Platron\FirstOfdV5\services\CreateReceiptResponse;
use Platron\FirstOfdV5\services\GetStatusRequest;
use Platron\FirstOfdV5\services\GetStatusResponse;
use Platron\FirstOfdV5\services\GetTokenRequest;
use Platron\FirstOfdV5\services\GetTokenResponse;

class CreateCorrectionTest extends IntegrationTestBase
{
	public function testCreateCorrection()
	{
		$client = new PostClient();
		$client->addLogger(new TestLogger());

		$tokenService = $this->createTokenRequest();
		$tokenResponse = new GetTokenResponse($client->sendRequest($tokenService));

		$this->assertTrue($tokenResponse->isValid());

		$createReceiptRequest = $this->createCorrectionRequest($tokenResponse->token);
		$createReceiptResponse = new CreateReceiptResponse($client->sendRequest($createReceiptRequest));

		$this->assertTrue($createReceiptResponse->isValid());

		$getStatusRequest = $this->createGetStatusRequest($createReceiptResponse, $tokenResponse);

		if (!$this->checkCorrectionStatus($client, $getStatusRequest)) {
			$this->fail('Correction don`t change status');
		}
	}

	/**
	 * @param string $token
	 * @return CreateCorrectionRequest
	 */
	private function createCorrectionRequest($token)
	{
		$correction = $this->createCorrection();
		$externalId = time();
		$createCorrectionRequest = new CreateCorrectionRequest($token, $this->groupCode, $externalId, $correction);
		$createCorrectionRequest->setDemoMode();
		return $createCorrectionRequest;
	}

	/**
	 * @return GetTokenRequest
	 */
	private function createTokenRequest()
	{
		$tokenRequest = new GetTokenRequest($this->login, $this->password);
		$tokenRequest->setDemoMode();
		return $tokenRequest;
	}

	/**
	 * @param $createReceiptResponse
	 * @param $tokenResponse
	 * @return GetStatusRequest
	 */
	private function createGetStatusRequest($createReceiptResponse, $tokenResponse)
	{
		$getStatusRequest = new GetStatusRequest($this->groupCode, $createReceiptResponse->uuid, $tokenResponse->token);
		$getStatusRequest->setDemoMode();
		return $getStatusRequest;
	}

	/**
	 * @return Client
	 */
	private function createCustomer()
	{
		$customer = new Client();
		$customer->addEmail('test@test.ru');
		$customer->addPhone('79050000000');
		$customer->addBirthdate("18.11.1990");
		$customer->addCitizenship("643");
		$customer->addDocumentCode("21");
		$customer->addDocumentData("4507 443564");
		$customer->addAddress("г.Москва, Ленинский проспект д.1 кв 43");
		return $customer;
	}

	/**
	 * @return Company
	 */
	private function createCompany()
	{
		$company = new Company(
			'test@test.ru',
			new SnoTypes(SnoTypes::ESN),
			$this->inn,
			$this->paymentAddress
		);
		return $company;
	}

	/**
	 * @return CorrectionInfo
	 */
	private function createCorrectionInfo()
	{
		$correctionInfo = new CorrectionInfo(
			new CorrectionTypes(CorrectionTypes::SELF),
			new \DateTime(),
			null
		);
		return $correctionInfo;
	}

	/**
	 * @return Payment
	 */
	private function createPayment()
	{
		$payment = new Payment(
			new PaymentTypes(PaymentTypes::ELECTRON),
			100
		);
		return $payment;
	}

	/**
	 * @return Vat
	 */
	private function createVat()
	{
		$vat = new Vat(new Vates(Vates::VAT10));
		$vat->addSum(100);
		return $vat;
	}

	/**
	 * @return Item
	 */
	private function createItem()
	{
		$vat = $this->createVat();
		$item = new Item(
			'Test Product',
			100,
			1,
			$vat,
			new Measures(Measures::ONE),
			new PaymentMethods(PaymentMethods::FULL_PAYMENT),
			new PaymentObjects(PaymentObjects::EXCISE)
		);
		$agentInfo = $this->createAgentInfo();
		$item->addAgentInfo($agentInfo);

		$item->addMarkProcessingMode(0);

		$markQuantity = $this->createMarkQuantity();
		$item->addMarkQuantity($markQuantity);

		$code = "MDEwNDYwNzQyODY3OTA5MDIxNmVKSWpvV0g1NERkVSA5MWZmZDAgOTJzejZrU1BpckFwZk1CZnR2TGJvRTFkbFdDLzU4aEV4UVVxdjdCQmtabWs0PQ==";
		$markCode = new MarkCode(
			new MarkCodeTypes(MarkCodeTypes::GS1M),
			$code);
		$item->addMarkCode($markCode);

		$sectoral_item_props = $this->createSectoralItemProps();
		$item->addSectoralItemProps([$sectoral_item_props]);
		$item->addUserData('Test user data');
		$item->addExcise(5.64);
		$item->addCountryCode("643");
		$item->addDeclarationNumber("10702020/060520/0013422");
		return $item;
	}

	/**
	 * @return SectoralItemProps
	 */
	private function createSectoralItemProps()
	{
		$sectoral_item_props = new SectoralItemProps("003");
		$sectoral_item_props->addDate("12.05.2020");
		$sectoral_item_props->addNumber("123/43");
		$sectoral_item_props->addValue("id1=val1&id2=val2&id3=val3");
		return $sectoral_item_props;
	}

	/**
	 * @return AgentInfo
	 */
	private function createAgentInfo()
	{
		$supplier = $this->createSupplier();
		$agentInfo = new AgentInfo(
			new AgentTypes(AgentTypes::ANOTHER),
			$supplier
		);
		$payingAgent = $this->createPayingAgent();
		$agentInfo->addPayingAgent($payingAgent);
		$moneyTransferOperator = $this->createMoneyTransferOperator();
		$receivePaymentOperator = $this->createReceivePaymentOperator();
		$agentInfo->addMoneyTransferOperator($moneyTransferOperator);
		$agentInfo->addReceivePaymentsOperator($receivePaymentOperator);
		return $agentInfo;
	}

	/**
	 * @return PayingAgent
	 */
	private function createPayingAgent()
	{
		$payingAgent = new PayingAgent('Operation name');
		$payingAgent->addPhone('79050000003');
		$payingAgent->addPhone('79050000004');
		return $payingAgent;
	}

	/**
	 * @return MarkQuantity
	 */
	private function createMarkQuantity()
	{
		$markQuantity = new MarkQuantity();
		$markQuantity->addNumerator(1);
		$markQuantity->addDenominator(2);
		return $markQuantity;
	}

	/**
	 * @return Supplier
	 */
	private function createSupplier()
	{
		$supplier = new Supplier('Supplier name');
		$supplier->addInn($this->inn);
		$supplier->addPhone('79050000001');
		$supplier->addPhone('79050000002');
		return $supplier;
	}

	/**
	 * @return MoneyTransferOperator
	 */
	private function createMoneyTransferOperator()
	{
		$moneyTransferOperator = new MoneyTransferOperator('Test moneyTransfer operator');
		$moneyTransferOperator->addInn($this->inn);
		$moneyTransferOperator->addPhone('79050000005');
		$moneyTransferOperator->addAddress('site.ru');
		return $moneyTransferOperator;
	}

	/**
	 * @return ReceivePaymentsOperator
	 */
	private function createReceivePaymentOperator()
	{
		$receivePaymentOperator = new ReceivePaymentsOperator('79050000006');
		$receivePaymentOperator->addPhone('79050000007');
		return $receivePaymentOperator;
	}

	/**
	 * @return Correction
	 */
	private function createCorrection()
	{
		$customer = $this->createCustomer();
		$company = $this->createCompany();
		$correctionInfo = $this->createCorrectionInfo();
		$payment = $this->createPayment();
		$vat = $this->createVat();
		$item = $this->createItem();
		$correction = new Correction(
			new CorrectionOperationTypes(CorrectionOperationTypes::BUY_CORRECTION),
			$customer,
			$company,
			$correctionInfo,
			$payment,
			$vat,
			[$item]
		);
		return $correction;
	}

	/**
	 * @param PostClient $client
	 * @param GetStatusRequest $getStatusRequest
	 * @return bool
	 * @throws SdkException
	 */
	private function checkCorrectionStatus(PostClient $client, GetStatusRequest $getStatusRequest)
	{
		for ($second = 0; $second <= 20; $second++) {
			$getStatusResponse = new GetStatusResponse($client->sendRequest($getStatusRequest));
			if ($getStatusResponse->isReceiptReady()) {
				$this->assertTrue($getStatusResponse->isValid());
				return true;
			} else {
				$second++;
			}
			sleep(1);
		}
		return false;
	}
}