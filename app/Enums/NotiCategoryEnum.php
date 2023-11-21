<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class NotiCategoryEnum extends Enum implements LocalizedEnum
{
    const Topics = 1;
    const Condition = 2;
    const Devices = 3;

    public static function getNotiCategory() {
        return [
            self::Topics    => self::getDescription(self::Topics),
            self::Condition     => self::getDescription(self::Condition),
            self::Devices    => self::getDescription(self::Devices),
        ];
    }
   
}
