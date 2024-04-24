<?php

namespace Platron\FirstOfdV5\data_objects;

use Platron\FirstOfdV5\handbooks\PaymentTypes;

class Payment extends BaseDataObject
{
	/** @var string */
	protected $type;
	/** @var float */
	protected $sum;

	/**
	 * Payment constructor.
	 * @param PaymentTypes $paymentType
	 * @param double $sum
	 */
	public function __construct(PaymentTypes $paymentType, $sum)
	{
		$this->sum = (double)$sum;
		$this->type = $paymentType->getValue();
	}
}