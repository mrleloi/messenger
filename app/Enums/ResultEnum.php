<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;


final class ResultEnum extends Enum
{
    const Pass = 1;
    const Fail = 2;
    const Wait = 3;

    public static function getResult() {
        return [
            self::Pass    => self::getDescription(self::Pass),
            self::Fail    => self::getDescription(self::Fail),
            self::Wait    => self::getDescription(self::Wait),
        ];
    }
}
