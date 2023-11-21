<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class StatusEnum extends Enum implements LocalizedEnum
{
    const All =   0;
    const Enable =   1;
    const Disable =  2;
    const Normal = 3;
    const Medium = 4;
    const Verify = 5;

    public static function getFaqStatus() {
        return [
            self::Enable     => self::getDescription(self::Enable),
            self::Disable    => self::getDescription(self::Disable),
        ];
    }

    public static function getAdminStatus() {
        return [
            self::Enable     => self::getDescription(self::Enable),
            self::Disable    => self::getDescription(self::Disable),
        ];
    }

    public static function getCompanyTypeStatus() {
        return [
            self::Enable     => self::getDescription(self::Enable),
            self::Disable    => self::getDescription(self::Disable),
        ];
    }

    public static function getJobStatus() {
        return [
            self::Enable     => self::getDescription(self::Enable),
            self::Disable    => self::getDescription(self::Disable),
        ];
    }

    public static function getAllowanceStatus() {
        return [
            self::Enable     => self::getDescription(self::Enable),
            self::Disable    => self::getDescription(self::Disable),
        ];
    }

    public static function getStatus() {
        return [
            self::Enable     => self::getDescription(self::Enable),
            self::Disable    => self::getDescription(self::Disable),
        ];
    }

    public static function getNotiConfirm() {
        return [
            self::Enable     => self::getDescription(self::Enable),
            self::Disable    => self::getDescription(self::Disable),
            self::Normal   => self::getDescription(self::Normal),
            self::Medium    => self::getDescription(self::Medium),
            self::Verify     => self::getDescription(self::Verify),
        ];
    }
}
