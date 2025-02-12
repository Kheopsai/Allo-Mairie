<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static User()
 */
final class RoleEnum extends Enum
{
    const Admin = 'admin';
    const User = 'user';


    public static function label($value): string
    {
        return self::labels()[$value] ?? 'Unknown';
    }

    public static function labels(): array
    {
        return [
            self::Admin => trans('Admin'),
            self::User => trans('User'),
        ];
    }
}
