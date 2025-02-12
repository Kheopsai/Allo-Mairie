<?php

namespace App\Observers;

use App\Models\Metropole;
use App\Models\Tenant;
use Illuminate\Support\Str;

class MetropoleObserver
{
    /**
     * Handle the Metropole "created" event.
     */
    public function created(Metropole $metropole): void
    {
        // $tenant = new Tenant();
        // $tenant->id=Str::uuid7()->toString();
        // $tenant->metropole_id= $metropole->id;
        // $tenant->save();
        $tenant = Tenant::create([
            'id' => Str::uuid7()->toString(),
            'metropole_id' => $metropole->id,
        ]);
        $uniqueDomain = $this->generateUniqueDomain($metropole);
        $tenant->createDomain($uniqueDomain);
    }

    /**
     * Handle the Metropole "updated" event.
     */
    public function updated(Metropole $metropole): void
    {
        //
    }

    /**
     * Handle the Metropole "deleted" event.
     */
    public function deleted(Metropole $metropole): void
    {
        //
    }

    /**
     * Handle the Metropole "restored" event.
     */
    public function restored(Metropole $metropole): void
    {
        //
    }

    /**
     * Handle the Metropole "force deleted" event.
     */
    public function forceDeleted(Metropole $metropole): void
    {
        //
    }

    protected function generateUniqueDomain(Metropole $metropole): string
    {
        $baseDomain = Str::slug($metropole->name, '-');
        $count = Tenant::whereHas('domains', fn($query) => $query->where('domain', 'like', $baseDomain . '%'))->count();
        return $count > 0 ? $baseDomain . '-' . ($count + 1) : $baseDomain;
    }
}
