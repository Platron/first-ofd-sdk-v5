<?php

namespace Platron\FirstOfdV5\services;

abstract class CreateDocumentResponse extends BaseServiceResponse
{
	/** @var string Уникальный идентификатор */
	public $uuid;

	/** @var string */
	public $status;
}