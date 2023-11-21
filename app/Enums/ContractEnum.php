<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class ContractEnum extends Enum implements LocalizedEnum
{
    const All       = 0;
    const Completed = 1;
    const InProcess = 2;
    const Canceled  = 3;
    const Extend    = 4;

    public static function getContractEnum() {
        return [
            self::Completed    => self::getDescription(self::Completed),
            self::InProcess    => self::getDescription(self::InProcess),
            self::Canceled     => self::getDescription(self::Canceled),
            self::Extend       => self::getDescription(self::Extend),
        ];
    }
}
