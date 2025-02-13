<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SourceEnum extends Enum
{
    const Text= "text";
    const File = "file";
    const Url = "url";
}
