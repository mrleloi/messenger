<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class NotiTypeEnum extends Enum implements LocalizedEnum
{
    const All = 1;
    const Web = 2;
    const Android = 3;
    const Ios = 4;

    public static function getNotiType() {
        return [
            self::All   => self::getDescription(self::All),
            self::Web    => self::getDescription(self::Web),
            self::Android     => self::getDescription(self::Android),
            self::Ios     => self::getDescription(self::Ios),
        ];
    }
}
