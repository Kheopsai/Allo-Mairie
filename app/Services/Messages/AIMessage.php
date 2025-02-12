<?php

namespace App\Services\Messages;

class AIMessage extends BaseMessage
{
    /**
     * Formats the message as ChatML.
     *
     * @return string
     */
    public function formatChatML(): string
    {
        return "<|im_start|>assistant\n";
    }

    /**
     * Returns the type of the message, used for serialization.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'ai';
    }
}
