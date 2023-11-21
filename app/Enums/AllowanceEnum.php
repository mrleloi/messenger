<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class AllowanceEnum extends Enum implements LocalizedEnum
{
    const Transportation = 1;
    const HealthExamination = 2;

    public static function getAllowanceType() {
        return [
            self::Transportation => self::getDescription(self::Transportation),
            self::HealthExamination => self::getDescription(self::HealthExamination),
        ];
    }
}
