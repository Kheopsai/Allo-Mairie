<?php

return [
    'model' => env('OLLAMA_MODEL', 'yemmiismail/merged_q4'),
    'url' => env('OLLAMA_URL', 'http://51.159.147.171:11434/api/generate'),
    'embed' => env('OLLAMA_EMBED', 'http://51.159.147.171:11434'),
    'chat' => env('OLLAMA_CHAT', 'http://51.159.147.171:11434'),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Hello, how can I assist you today?'),
    'connection' => [
        'timeout' => env('OLLAMA_CONNECTION_TIMEOUT', 3600),
    ],
];
