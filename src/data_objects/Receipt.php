<?php

namespace Platron\AtolV4\data_objects;

use Platron\AtolV4\handbooks\ReceiptOperationTypes;

class Receipt extends BaseDataObject
{
	/** @var Client */
	protected $client;
	/** @var Company */
	protected $company;
	/** @var Item[] */
	private $items;
	/** @var Payment[] */
	private $payments;
	/** @var string */
	private $operationType;

	/**
	 * Document constructor.
	 * @param Client $client
	 * @param Company $company
	 * @param Item[] $items
	 * @param Payment $payment
	 * @param ReceiptOperationTypes $type
	 */
	public function __construct(Client $client, Company $company, $items, Payment $payment, ReceiptOperationTypes $type)
	{
		$this->client = $client;
		$this->company = $company;
		foreach($items as $item) {
			$this->addItem($item);
		}
		$this->addPayment($payment);
		$this->operationType = $type->getValue();
	}

	/**
	 * @param Item $item
	 */
	private function addItem(Item $item)
	{
		$this->items[] = $item;
	}

	/**
	 * @param Payment $payment
	 */
	public function addPayment(Payment $payment)
	{
		$this->payments[] = $payment;
	}

	/**
	 * @return float
	 */
	private function getItemsAmount()
	{
		$itemsAmount = 0;
		foreach ($this->items as $item) {
			$itemsAmount += $item->getPositionSum();
		}
		return $itemsAmount;
	}

	/**
	 * @return string
	 */
	public function getOperationType(){
		return $this->operationType;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		$params = parent::getParameters();

		foreach($this->items as $item) {
			$params['items'][] = $item->getParameters();
		}
		foreach($this->payments as $payment) {
			$params['payments'][] = $payment->getParameters();
		}

		$params['total'] = (double)$this->getItemsAmount();

		return $params;
	}
}