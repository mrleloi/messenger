<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class NotiConfirmEnum extends Enum implements LocalizedEnum
{
    const Normal = 1;
    const Medium = 2;
    const Verify = 3;

    public static function getNotiConfirm() {
        return [
            self::Normal   => self::getDescription(self::Normal),
            self::Medium    => self::getDescription(self::Medium),
            self::Verify     => self::getDescription(self::Verify),
        ];
    }
}
