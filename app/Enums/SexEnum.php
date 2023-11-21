<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class SexEnum extends Enum implements LocalizedEnum
{
    const Female    = 0;
    const Male      = 1;
    const Other     = 2;

    public static function getSexEnum() {
        return [
            self::Female    => self::getDescription(self::Female),
            self::Male      => self::getDescription(self::Male),
            self::Other     => self::getDescription(self::Other),
        ];
    }
}
