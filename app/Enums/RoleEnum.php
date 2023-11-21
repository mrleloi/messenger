<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class RoleEnum extends Enum implements LocalizedEnum
{
    const sadmin = 'sadmin';
    const admin = 'admin';
    const staff = 'staff';

    const employee = 'employee';
    const chief = 'chief';

    const all = 'all';

    public static function getAdminRole() {
        return [
            self::sadmin     => self::getDescription(self::sadmin),
            self::admin    => self::getDescription(self::admin),
            self::staff    => self::getDescription(self::staff),
        ];
    }

    public static function getEmployeeRole() {
        return [
            self::employee     => self::getDescription(self::employee),
            self::chief    => self::getDescription(self::chief),
        ];
    }

    public static function getFaqRole() {
        return [
            self::sadmin     => self::getDescription(self::sadmin),
            self::admin    => self::getDescription(self::admin),
            self::staff    => self::getDescription(self::staff),
            self::employee     => self::getDescription(self::employee),
            self::chief    => self::getDescription(self::chief),
        ];
    }

    public static function getNotiReceiver() {
        return [
            self::all    => self::getDescription(self::all),
            self::admin    => self::getDescription(self::admin),
            self::employee     => self::getDescription(self::employee),
        ];
    }

}
