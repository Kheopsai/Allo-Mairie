<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\NotASubdomainException;

trait CheckDomain
{
    /**
     * The index of the subdomain fragment in the hostname
     * split by `.`. 0 for first fragment, 1 if you prefix
     * your subdomain fragments with `www`.
     */
    public static int $subdomainIndex = 0;

    public function getTenant(Request $request): ?Tenant
    {
        if ($this->isSubdomain($request->getHost())) {
            $domain = $this->makeSubdomain($request->getHost());
        } else {
            $domain = $request->getHost();
        }

        return config('tenancy.tenant_model')::query()
            ->whereHas('domains', function (Builder $query) use ($domain) {
                $query->where('domain', $domain);
            })
            ->with('domains')
            ->first();
    }

    protected function isSubdomain(string $hostname): bool
    {
        return Str::endsWith($hostname, config('tenancy.central_domains'));
    }

    protected function makeSubdomain(string $hostname): NotASubdomainException|string
    {
        $parts = explode('.', $hostname);

        $isLocalhost = count($parts) === 1;
        $isIpAddress = count(array_filter($parts, 'is_numeric')) === count($parts);

        // If we're on localhost or an IP address, then we're not visiting a subdomain.
        $isACentralDomain = in_array($hostname, config('tenancy.central_domains'), true);
        $notADomain = $isLocalhost || $isIpAddress;
        $thirdPartyDomain = ! Str::endsWith($hostname, config('tenancy.central_domains'));

        if ($isACentralDomain || $notADomain || $thirdPartyDomain) {
            return new NotASubdomainException($hostname);
        }

        return $parts[static::$subdomainIndex];
    }
}
