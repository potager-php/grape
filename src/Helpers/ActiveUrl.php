<?php

namespace Potager\Grape\Helpers;

class ActiveUrl
{
    public static function validate(string $string): bool
    {
        if (!Url::validate($string)) {
            return false;
        }

        $host = parse_url($string, PHP_URL_HOST);
        if (!$host || !is_string($host)) {
            return false;
        }

        // Fast DNS record check (A, AAAA, CNAME) avoids native PHP warning when resolving non-existent host
        if (!checkdnsrr($host, 'A') && !checkdnsrr($host, 'AAAA') && !checkdnsrr($host, 'CNAME')) {
            return false;
        }

        $headers = @get_headers($string);
        return is_array($headers) && count($headers) > 0;
    }
}