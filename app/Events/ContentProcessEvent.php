<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ContentProcessEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, InteractsWithSockets, Queueable, SerializesModels;

    public int $id;


    public string $index;

    public string $text;

    public int $user_id;

    public function __construct(int $id, string $index, string $text,int $user_id)
    {
        $this->id = $id;
        $this->index = $index;
        $this->text = $text;
        $this->user_id= $user_id;
    }
}
