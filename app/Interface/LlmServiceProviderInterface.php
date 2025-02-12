<?php

namespace App\Interface;

interface LlmServiceProviderInterface
{
    public function getIteration(string $message): \Generator;

    public function getResponse(string $messsage): string;

    public function getGeneratedText(): string;
}
