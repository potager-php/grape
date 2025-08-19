<?php

namespace Potager\Grape\Helpers;

use Potager\Grape\Grape;

class HelpersFacade
{
    public function isTrue($mixed): bool
    {
        return $mixed === true || in_array($mixed, Grape::getTruthy(), true);
    }

    public function isFalse($mixed): bool
    {
        return $mixed === false || in_array($mixed, Grape::getFalsy(), true);
    }

    public function isUrl(string $url): bool
    {
        return Url::validate($url);
    }

    public function isActiveUrl(string $url): bool
    {
        return ActiveUrl::validate($url);
    }

    public function isJson(string $json): bool
    {
        return Json::validate($json);
    }

    public function isLuhnNumber(string $number): bool
    {
        return LuhnNumber::validate($number);
    }

    public function isIp(string $ip, ?string $version = null): bool
    {
        return IP::validate($ip, $version);
    }

    public function isIpv4(string $ip): bool
    {
        return IP::validate($ip, 'ipv4');
    }

    public function isIpv6(string $ip): bool
    {
        return IP::validate($ip, 'ipv6');
    }

    public function isMobilePhone(string $phone, ?array $locales = null, bool $strict = false): bool
    {
        return MobilePhone::validate($phone, $locales, $strict);
    }

    public function isCreditCard(string $number, ?array $providers = null): bool
    {
        return CreditCard::validate($number, $providers);
    }
}