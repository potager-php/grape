<?php

namespace Potager\Grape\Exceptions;

use Throwable;

class InvalidSchemaException extends \Exception
{
    public function __construct(string $message = "Invalid schema provided", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
