<?php

namespace Platron\FirstOfdV5\tests\unit\data_objects;

use PHPUnit\Framework\TestCase;
use Platron\FirstOfdV5\data_objects\Client;
use Platron\FirstOfdV5\data_objects\Company;
use Platron\FirstOfdV5\data_objects\Item;
use Platron\FirstOfdV5\data_objects\Payment;
use Platron\FirstOfdV5\data_objects\Receipt;
use Platron\FirstOfdV5\handbooks\ReceiptOperationTypes;

class ReceiptTest extends TestCase
{
	public function test_getParameters_withoutAdditionalCheckProps_resultHasNoKey()
	{
		$receipt = $this->createReceipt();

		$this->assertArrayNotHasKey('additional_check_props', $receipt->getParameters());
	}

	public function test_getParameters_withAdditionalCheckProps_resultHasAppropriateValue()
	{
		$receipt = $this->createReceipt();

		$receipt->setAdditionalCheckProps('additional');

		$this->assertEquals($receipt->getParameters()['additional_check_props'], 'additional');
	}

	public function test_setAdditionalCheckProps_notString_expectInvalidArgumentException()
	{
		$receipt = $this->createReceipt();

		$this->expectException(\InvalidArgumentException::class);

		$receipt->setAdditionalCheckProps(123);
	}

	public function test_setAdditionalCheckProps_longString_expectLengthException()
	{
		$receipt = $this->createReceipt();

		$this->expectException(\LengthException::class);

		$receipt->setAdditionalCheckProps('12345678901234567');
	}

	/**
	 * @return Receipt
	 */
	private function createReceipt()
	{
		return new Receipt($this->createMock(Client::class), $this->createMock(Company::class),
			[$this->createMock(Item::class)], $this->createMock(Payment::class),
			$this->createMock(ReceiptOperationTypes::class));
	}
}
