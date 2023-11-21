<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FriendStatus extends Enum
{
    const Pending =   0;
    const Accepted =   1;
    const Rejected = 2;
    const Blocked = 3;

    public static function getDescription($value): string
    {
        if ($value === self::Pending) {
            return 'Pending';
        }
        if ($value === self::Accepted) {
            return 'Accepted';
        }
        if ($value === self::Rejected) {
            return 'Rejected';
        }
        if ($value === self::Blocked) {
            return 'Blocked';
        }
        return parent::getDescription($value);
    }

    public static function getFriendStatus() {
        return [
            self::Pending   => self::getDescription(self::Pending),
            self::Accepted    => self::getDescription(self::Accepted),
            self::Rejected     => self::getDescription(self::Rejected),
            self::Blocked     => self::getDescription(self::Blocked),
        ];
    }
}
