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

    public int $user_id;

    public function __construct(int $id,string $url,int $user_id)
    {
        $this->id=$id;
        $this->url=$url;
        $this->user_id= $user_id;
    }

}
