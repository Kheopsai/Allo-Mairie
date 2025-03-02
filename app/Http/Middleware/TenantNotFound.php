<?php

namespace App\Http\Middleware;

use App\Traits\CheckDomain;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\HttpFoundation\Response;

class TenantNotFound
{
    use CheckDomain;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isSubdomain($request->getHost())) {
            $domain = $this->makeSubdomain($request->getHost());
        } else {
            $domain = $request->getHost();
        }
        $tenant = config('tenancy.tenant_model')::query()
            ->whereHas('domains', function (Builder $query) use ($domain) {
                $query->where('domain', $domain);
            })
            ->with('domains')
            ->first();
        if (! $tenant) {
            return redirect(config('app.url'));
        }
        $database = $tenant->database()->getName();
        if (! $tenant->database()->manager()->databaseExists($database)) {
            return redirect()->route('tenant.provisioning', ['tenant' => $tenant]);
        }

        return $next($request);
    }
}
