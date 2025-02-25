<?php

namespace App\Services\Tokens;

class TokenService
{
    public function countTokens(string $text): int
    {
        $wordCount = str_word_count($text);

        return (int) ceil($wordCount * 1.3);
    }
    public function calculateTotalTokens($input, $output): int
    {
        return $input + $output;
    }
}
