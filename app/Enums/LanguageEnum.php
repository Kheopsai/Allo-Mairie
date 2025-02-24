<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class LanguageEnum extends Enum
{
    public const string Arabic = 'arabic';
    public const string French = 'french';
    public const string English = 'english';
}
