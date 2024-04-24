<?php

namespace Platron\FirstOfdV5\clients;

use Platron\FirstOfdV5\services\BaseServiceRequest;
use stdClass;

interface iClient
{

	/**
	 * Послать запрос
	 * @param BaseServiceRequest $service
	 * @return stdClass
	 */
	public function sendRequest(BaseServiceRequest $service);
}
