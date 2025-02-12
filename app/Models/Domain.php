<?php

namespace App\Models;

use App\Traits\HasDomain;
use Stancl\Tenancy\Database\Models\Domain as DomainTenant;

class Domain extends DomainTenant
{
    use HasDomain;
}
