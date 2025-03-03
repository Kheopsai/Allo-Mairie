<?php

namespace App\Actions\Tenant\Backend;

use App\Services\Messages\TemplateMessage;
use Kambo\Langchain\Prompts\SystemMessagePromptTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class Chat
{
    use AsAction;

    private static ?string $prompt_system = null;
    private static ?string $prompt_user = null;
    private static ?array $inputs = null;

    private mixed $context;

    public function __construct($context = null)
    {
        $this->context = $context;
        self::init();
    }

    public function handle($text): string
     {
        return self::handleStatic($text,$this->context);
    }

    public static function handleStatic($text,$context=null): string
    {
        self::init();
        $prompt = new TemplateMessage(self::$prompt_system,self::$prompt_user,self::$inputs);
        $systemMessagePrompt = new SystemMessagePromptTemplate($prompt->format()->generatePrompt());

        return $systemMessagePrompt->format(['context'=> $context,'text'=> $text])->getContent();
    }

    private static function init(): void
    {
        self::$prompt_system = config('kheops.ai.chat.prompt_system');
        self::$prompt_user = config('kheops.ai.chat.prompt_user');
        self::$inputs = config('kheops.ai.chat.inputs');
    }
}
