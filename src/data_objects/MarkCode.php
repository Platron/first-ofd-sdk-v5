<?php

namespace Platron\FirstOfdV5\data_objects;

use Platron\FirstOfdV5\handbooks\MarkCodeTypes;

class MarkCode extends BaseDataObject
{
	/** @var string */
	protected $type;
	/** @var string */
	protected $value;

	/**
	 * MarkCode constructor.
	 * @param MarkCodeTypes $markCodeType
	 * @param string $value
	 */
	public function __construct(MarkCodeTypes $markCodeType, $value)
	{
		$this->type = $markCodeType->getValue();
		$this->value = (string)$value;
	}

	public function getParameters()
	{
		$field = [];
		$field[$this->type] = $this->value;
		return $field;
	}

}

