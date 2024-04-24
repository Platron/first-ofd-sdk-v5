<?php

namespace Platron\FirstOfdV5\data_objects;

use Platron\FirstOfdV5\handbooks\CorrectionTypes;

class CorrectionInfo extends BaseDataObject
{
	/** @var string */
	protected $type;
	/** @var string */
	protected $base_date;
	/** @var string */
	protected $base_number;

	/**
	 * CorrectionInfo constructor
	 * @param CorrectionTypes $type
	 * @param \DateTime $baseDate
	 * @param string $baseNumber
	 */
	public function __construct(CorrectionTypes $type, \DateTime $baseDate, $baseNumber)
	{
		$this->type = $type->getValue();
		$this->base_date = $baseDate->format('d.m.Y H:i:s');
		$this->base_number = (string)$baseNumber;
	}
}