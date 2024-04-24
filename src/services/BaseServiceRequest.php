<?php

namespace Platron\FirstOfdV5\services;

use Platron\FirstOfdV5\data_objects\BaseDataObject;

abstract class BaseServiceRequest extends BaseDataObject
{
	/** @var bool */
	private $demoMode;

	const
		REQUEST_URL = 'https://external-api-farm.1-ofd.ru/',
		REQUEST_DEMO_URL = 'https://external-api-farm.1-ofd-test.ru/';

	/**
	 * Получить url ждя запроса
	 * @return string
	 */
	abstract public function getRequestUrl();

	/**
	 * @return string
	 */
	protected function getBaseUrl()
	{
		return $this->demoMode ? self::REQUEST_DEMO_URL : self::REQUEST_URL;
	}

	public function setDemoMode()
	{
		$this->demoMode = true;
	}

	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return [
			'Content-type: application/json; charset=utf-8'
		];
	}
}
