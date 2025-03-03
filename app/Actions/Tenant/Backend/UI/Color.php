<?php

namespace App\Actions\Tenant\Backend\UI;

use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class Color
{
    use AsAction;

    public function handle():JsonResponse
    {
        $tenant = tenant();
        if ($tenant) {
            $setting = $tenant->setting;
            if ($setting) {
                return response()->json(['primaryColor' => $setting->primary_color]);
            }
            return response()->json(['primaryColor' => '#8b5cf6']);
        }
        return response()->json(['primaryColor' => '#8b5cf6']);
    }
}
