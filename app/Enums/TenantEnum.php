<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Provisioning()
 * @method static static Active()
 */
final class TenantEnum extends Enum
{
    const string Provisioning = 'provisioning';
    const string Active = 'active';
}
