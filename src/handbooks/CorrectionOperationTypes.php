<?php

namespace Platron\FirstOfdV5\handbooks;

use MyCLabs\Enum\Enum;

class CorrectionOperationTypes extends Enum
{
	const
		SELL_CORRECTION = 'sell_correction',
		BUY_CORRECTION = 'buy_correction';
}