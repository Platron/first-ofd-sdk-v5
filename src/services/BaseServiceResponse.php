<?php

namespace Platron\FirstOfdV5\services;

use stdClass;

abstract class BaseServiceResponse
{

	/** @var string uuid */
	protected $errorId;
	/** @var int */
	protected $errorCode;

	/** @var string */
	protected $errorDescription;

	/**
	 * BaseServiceResponse constructor
	 * @param stdClass $response
	 */
	public function __construct(stdClass $response)
	{
		if ($this->hasStandardError($response)) {
			$this->setStandardError($response);
		}

		foreach (get_object_vars($this) as $name => $value) {
			if (!empty($response->$name)) {
				$this->$name = $response->$name;
			}
		}
	}

	/**
	 * @param stdClass $response
	 * @return bool
	 */
	private function hasStandardError(stdClass $response)
	{
		return !empty($response->error->error_id);
	}

	/**
	 * @param stdClass $response
	 */
	private function setStandardError(stdClass $response)
	{
		$this->errorId = $response->error->error_id;
		$this->errorCode = $response->error->code;
		$this->errorDescription = 'Error type ' . $response->error->type . ' error code ' . $response->error->code . ' ' . $response->error->text;
		if (!empty($response->error->error_id)) {
			$this->errorDescription .= '. Error id ' . $response->error->error_id;
		}
	}

	/**
	 * Проверка на ошибки в ответе
	 * @return boolean
	 */
	public function isValid()
	{
		if (!empty($this->errorId)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Получить код ошибки из ответа
	 * @return int
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}

	/**
	 * Получить описание ошибки из ответа
	 * @return string
	 */
	public function getErrorDescription()
	{
		return $this->errorDescription;
	}
}
