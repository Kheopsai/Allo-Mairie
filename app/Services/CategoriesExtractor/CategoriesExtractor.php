<?php

namespace App\Services\CategoriesExtractor;

use App\Services\Messages\TemplateMessage;
use Kambo\Langchain\Prompts\SystemMessagePromptTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class CategoriesExtractor
{
    use AsAction;

    private string $prompt_system;
    private string $prompt_user;
    private mixed $inputs;


    public function __construct()
    {
        $this->prompt_system = config('kheops.ai.hubs.prompt_system');
        $this->prompt_user = config('kheops.ai.hubs.prompt_user');
        $this->inputs= config('kheops.ai.hubs.inputs');
    }

    public function handle($text,$categories): string
    {
        $prompt = new TemplateMessage($this->prompt_system,$this->prompt_user,$this->inputs);
        $systemMessagePrompt = new SystemMessagePromptTemplate($prompt->format()->generatePrompt());
        return $systemMessagePrompt->format(['categories'=> $categories,'text'=> $text])->getContent();
    }
}
