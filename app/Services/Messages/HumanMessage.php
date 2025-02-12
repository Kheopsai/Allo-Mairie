<?php

namespace App\Services\Messages;

class HumanMessage extends BaseMessage
{
    /**
     * Formats the message as ChatML.
     */
    public function formatChatML(): string
    {
        return "<|im_start|>user\n".$this->content."\n<|im_end|>";
    }

    /**
     * Returns the type of the message, used for serialization.
     */
    public function getType(): string
    {
        return 'human';
    }
}
