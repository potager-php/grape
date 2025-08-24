<?php

namespace Potager\Grape\Helpers;

use phpDocumentor\Reflection\Types\Array_;

class ActiveUrl
{
    public static function validate(string $string): bool
    {
        if (!Url::validate($string))
            return false;
        $headers = @get_headers($string);
        return is_array($headers);
    }
}