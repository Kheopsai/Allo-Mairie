<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class GovernmentInstitutionType extends Enum
{
    const Metropole = "metropole";
    const Mairie = "mairie";
}
