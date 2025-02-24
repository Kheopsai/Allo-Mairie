<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ModelEnum extends Enum
{
    public const string kHEOPS = 'kheops';
    public const string OPENIA = 'openai';
    public const string MISTRAL = 'mistral';
}
