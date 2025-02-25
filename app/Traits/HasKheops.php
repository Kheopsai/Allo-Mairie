<?php

namespace App\Traits;

use App\Facade\LlmManagerFacade;
use App\Services\Tokens\CreditService;
use App\Services\Tokens\TokenService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportStreaming\HandlesStreaming;

trait HasKheops
{
    use HandlesStreaming;

    public string $stream = 'content';

    protected CreditService $creditService;

    protected TokenService $tokenService;

    public function initializeHasKheops(): void
    {
        $this->creditService = app(CreditService::class);
        $this->tokenService = app(TokenService::class);
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     * @throws Exception
     * @throws Throwable
     */
    public function askKheops($store = false): void
    {
        $this->initializeHasKheops();

        $llm = LlmManagerFacade::build(config('llm.config'));
        $message = $this->pull('generatedMessage');

        $inputTokens = $this->tokenService->countTokens($message);
        $maxOutputTokens = config('llm.max_output_tokens');
        $creditsOutput = ceil($maxOutputTokens / $llm->conversion_rate);
        $creditsTotalEstimated = ceil($inputTokens / $llm->conversion_rate) + $creditsOutput;

        if (Auth::user()->credits->available_credits < $creditsTotalEstimated) {
            throw new Exception(trans('Insufficient funds for this request.'));
        }


        foreach ($llm->getIteration($message) as $item) {
            $this->dispatch('isStream');
            $this->dispatch('scrollToBottom');
            $this->stream($this->stream, nl2br($item));
            usleep(5000);
        }

        $this->content = $llm->getGeneratedText();

        $outputTokens = $this->tokenService->countTokens($this->content);

        $totalTokens = $this->tokenService->calculateTotalTokens($inputTokens, $outputTokens);

        $this->deductCredits($llm, $totalTokens);

        if ($store) {
            $this->store($this->content);
            $this->reset('isLoading');
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function generateKheops($key): void
    {
        $this->initializeHasKheops();

        $llm = LlmManagerFacade::build(config('llm.config'));

        $message = $this->generatedMessage;

        $inputTokens = $this->tokenService->countTokens($message);
        $maxOutputTokens = config('llm.max_output_tokens');
        $creditsOutput = ceil($maxOutputTokens / $llm->conversion_rate);
        $creditsTotalEstimated = ceil($inputTokens / $llm->conversion_rate) + $creditsOutput;
        if (Auth::user()->credits->available_credits < $creditsTotalEstimated) {
            throw new Exception(trans('Insufficient funds for this request.'));
        }

        $this->content[$key] = $llm->getResponse($message);

        $outputTokens = $this->tokenService->countTokens($this->content[$key]);
        $totalTokens = $this->tokenService->calculateTotalTokens($inputTokens, $outputTokens);
        $this->deductCredits($llm, $totalTokens);
    }

    /**
     * @throws Throwable
     */
    protected function deductCredits($model, $totalTokens): void
    {
        $user = auth()->user();
        $this->creditService->consumeCredits($user, $model, $totalTokens);
    }
}
