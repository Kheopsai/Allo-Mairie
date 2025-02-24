<?php

namespace App\Events\Documents;

use App\Enums\VectorStoreEnum;
use App\Models\Source;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DocumentProcessEvent implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels , InteractsWithQueue, Queueable;

    public string $tenant;

    public Source $source;

    public string $file;

    public string $provider;

    public int $user_id;

    public function __construct(string $tenant, Source $source,$user_id, string $provider = VectorStoreEnum::Postgres)
    {
        $this->tenant = $tenant;
        $this->source = $source;
        $this->provider = $provider;
        $this->user_id = $user_id;
    }

}
