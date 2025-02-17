<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapperProcessEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels, InteractsWithQueue, Queueable;

    public int $id;

    public string $url;

    public function __construct(int $id,string $url)
    {
        $this->id=$id;
        $this->url=$url;
    }

}
