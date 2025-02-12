<?php

namespace App\Traits;

use App\Facade\LlmManagerFacade;
use Livewire\Features\SupportStreaming\HandlesStreaming;

trait HasKheops
{
    use HandlesStreaming;

    public string $stream = 'content';

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     * @throws Exception
     * @throws Throwable
     */
    public function askKheops($store = false): void
    {
        $llm = LlmManagerFacade::build(config('llm.config'));
        $message = $this->pull('generatedMessage');


        foreach ($llm->getIteration($message) as $item) {
            $this->dispatch('isStream');
            $this->dispatch('scrollToBottom');
            $this->stream($this->stream, nl2br($item));
            usleep(5000);
        }

        $this->content = $llm->getGeneratedText();

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
        $llm = LlmManagerFacade::build(config('llm.config'));

        $message = $this->generatedMessage;
        $this->content[$key] = $llm->getResponse($message);
    }
}
