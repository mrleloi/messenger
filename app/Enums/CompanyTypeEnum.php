<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CompanyTypeEnum extends Enum
{
    const CustomerCompany = 1;
    const MyCompany = 2;

    public static function getCompanyType() {
        return [
            self::CustomerCompany  => self::getDescription(self::CustomerCompany),
            self::MyCompany    => self::getDescription(self::MyCompany),
        ];
    }
}
