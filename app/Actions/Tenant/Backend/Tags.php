<?php

namespace App\Actions\Tenant\Backend;

use App\Services\Messages\TemplateMessage;
use Kambo\Langchain\Prompts\SystemMessagePromptTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class Tags
{
    use AsAction;

    private string $prompt_system;
    private string $prompt_user;
    private mixed $inputs;

    public function __construct()
    {
        $this->prompt_system = config('kheops.ai.tags.prompt_system');
        $this->prompt_user = config('kheops.ai.tags.prompt_user');
        $this->inputs = config('kheops.ai.tags.inputs');
    }
    public function handle($text): string
    {
        $prompt = new TemplateMessage($this->prompt_system, $this->prompt_user, $this->inputs);
        $systemMessagePrompt = new SystemMessagePromptTemplate($prompt->format()->generatePrompt());
        return $systemMessagePrompt->format(['text' => $text])->getContent();
    }
}
