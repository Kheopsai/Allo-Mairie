<?php

return [
    'config' => env('LLM_MODEL','mistral'),
    'credits' => env('LLM_CREDITS',1000),
    'models'=> [
        'kheops'=> App\Services\Llm\HuggingFaceService::class,
        'mistral'=> App\Services\Llm\MistralService::class,
    ],
    'max_output_tokens'=> 2000,
];
