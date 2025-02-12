<?php

namespace App\Services\Messages;

use Illuminate\Support\Arr;
use Kambo\Langchain\Prompts\PromptTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class TemplateMessage
{
    use AsAction;
    private SystemMessage $system;
    private HumanMessage $human;
    private AIMessage $ai;
    private mixed $inputs;
    private string $format;

    public function __construct($prompt_system, $prompt_user, $inputs)
    {
        $this->system = new SystemMessage($prompt_system);
        $this->human = new HumanMessage($prompt_user);
        $this->ai = new AIMessage('');
        $this->inputs = $inputs;
    }

    public function format(): static
    {
        $this->format = Arr::join([$this->system->formatChatML(), $this->human->formatChatML(), $this->ai->formatChatML()], '\n');
        return $this;
    }

    public function generatePrompt(): PromptTemplate
    {
        return new PromptTemplate(
            $this->format,
            $this->inputs,
        );
    }
}
