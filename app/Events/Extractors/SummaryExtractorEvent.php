<?php

namespace App\Events\Extractors;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SummaryExtractorEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, InteractsWithSockets, Queueable, SerializesModels;

    public Model $model;

    public string $text;

    public ?string $input;

    public function __construct(Model $model, string $text, string $input)
    {
        $this->model = $model;
        $this->text = $text;
        $this->input = $input;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
