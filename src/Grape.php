<?php

namespace Potager\Grape;

use Potager\Grape\Traits\BehaviorConfiguration;
use Potager\Grape\Traits\BooleanLikeValues;
use Potager\Grape\Traits\CollectorConfiguration;
use Potager\Grape\Traits\DatabaseConfiguration;
use Potager\Grape\Traits\HelperManagement;
use Potager\Grape\Traits\MessageConfiguration;
use Potager\Grape\Traits\ValidatorFactory;

class Grape
{
    use BehaviorConfiguration;
    use BooleanLikeValues;
    use DatabaseConfiguration;
    use HelperManagement;
    use ValidatorFactory;
    use MessageConfiguration;
    use CollectorConfiguration;
}