<?php

require_once __DIR__ . '/vendor/autoload.php';

use Potager\Grape\Helpers\MobilePhone;

$invalidNumbers = [
    'en-US' => '+14155552672',
    'fr-FR' => '+33612345679',
    'de-DE' => '+4915123456780',
    'en-GB' => '+447911123457',
    'es-ES' => '+34612345679',
    'it-IT' => '+393331234568',
    'nl-NL' => '+31612345679',
    'pt-BR' => '+5511999999990',
    'ru-RU' => '+79111234560',
];
foreach ($invalidNumbers as $locale => $number) {
    $result = MobilePhone::validate($number, [$locale]);
    echo "Testing $number for locale $locale, result: $result\n";
}

?>