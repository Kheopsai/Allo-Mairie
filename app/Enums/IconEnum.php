<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class IconEnum extends Enum
{
    public const string Heroicon = 'heroicon';

    public const string Lucide = 'lucide';

    public static function hasStyle($style): bool
    {
        return match ($style) {
            self::Lucide => false,
            default => true
        };
    }
}
