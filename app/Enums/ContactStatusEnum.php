<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ContactStatusEnum extends Enum
{
    const All           =   0;
    const Processing    =   1;
    const Done          =   2;

    public static function getContactStatus() {
        return [
            self::Processing    => self::getDescription(self::Processing),
            self::Done          => self::getDescription(self::Done),
        ];
    }
}
