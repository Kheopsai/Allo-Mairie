<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Month()
 * @method static static Year()
 */
final class IntervalEnum extends Enum
{
    public const string Month = 'month';
    public const string Year = 'year';
}
