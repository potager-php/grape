<?php

namespace Potager\Grape\Helpers;

class Url
{
    public static function validate(string $str): bool
    {
        return filter_var($str, FILTER_VALIDATE_URL);
    }
}