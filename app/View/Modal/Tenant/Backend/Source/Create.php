<?php

namespace App\View\Modal\Tenant\Backend\Source;

use App\Actions\Tenant\Backend\Tags;
use App\Enums\SourceEnum;
use App\Events\ContentProcessEvent;
use App\Events\Documents\DocumentProcessEvent;
use App\Events\ScrapperProcessEvent;
use App\Jobs\Sources\ProcessDocumentJob;
use App\Models\Source;
use App\Responses\HuggingFace\HuggingFaceResponse;
use App\Services\Documents\TextExtractor;
use App\Services\Extractors\SummaryExtractor;
use AssistedMindfulness\Rake\Rake;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Str;

class Create extends ModalComponent
{

    use WireUiActions;
    use WithFileUploads;

    public $step = 1;

    public $value;

    public $file;


    public bool $loadContent = true;

    public $tags = [];

    public $tag;

    private SummaryExtractor $summaryExractor;
    private TextExtractor $textExtractor;

    private Tags $tagExtractor;


    #[Validate('required|min:3')]
    public $name;

    // #[Validate(['sometimes','string',"min:3"])]
    public $content;


    #[Validate('sometimes','url')]
    public $url;

    public function boot(): void
    {
        $this->summaryExractor = new SummaryExtractor;
        $this->textExtractor = new TextExtractor;
        $this->tagExtractor = new Tags;
    }


    public static function modalMaxWidth(): string
    {
        return '2xl';
    }

    public function setValue($value): void
    {
        $this->value = $value;
        $this->step = 2;
    }

    public function back(): void
    {
        $this->step = 1;
    }

    public function removeFile(): void
    {
        $this->reset('file');
    }

    /**
     * @throws Exception
     */
    public function updatedFile(): void
    {
        $this->validateFile();
        $this->js('$wire.getContentAndTags()');
        $this->loadContent();
    }

    public function loadContent(): void
    {
        $this->loadContent = ! $this->loadContent;
    }

    /**
     * @throws Exception
     */
    public function getContentAndTags(): void
    {

        try {
            $content = $this->textExtractor->extractText($this->file->getRealPath());
            $stupidContext = $this->generateContent($content);
            $this->getTags($stupidContext);
            $this->getSummarize($stupidContext);
            $this->loadContent();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            $this->loadContent();
        }
    }

    public function validateFile(): void
    {
        if ($this->file) {
            $this->validate([
                'file' => ['required', 'file'],
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function getSummarize($context): void
    {
        $prompt = $this->summaryExractor->handle($context);
        $this->content = $this->getResponse($prompt, 100);
    }

    /**
     * @throws Exception
     */
    public function getTags($context): void
    {
        $rake = new Rake(4, false);
        $this->tags = $rake->extract($context)->sortByScore('desc')->keywords();
    }

    public function estimateTokenCount($text): float
    {
        return ceil(strlen($text) / 2);
    }

    public function concatenateContextsWithLimit(array $contexts, $maxTokens = 4700): string
    {
        $concatenatedContext = '';
        $currentTokenCount = 0;
        foreach ($contexts as $context) {
            $context = preg_replace('/\s+/', ' ', trim($context));
            $contextTokenCount = $this->estimateTokenCount($context);
            if ($currentTokenCount + $contextTokenCount > $maxTokens) {
                break;
            }

            $concatenatedContext .= $context . "\n";
            $currentTokenCount += $contextTokenCount;
        }

        return trim($concatenatedContext);
    }

    public function generateContent($content): string
    {
        return $this->concatenateContextsWithLimit($content);
    }

    /**
     * @throws Exception
     */
    public function getResponse($prompt, $token = 300): string
    {
        $response = new HuggingFaceResponse($prompt, $token);

        return $response->getGeneratedText();
    }

    public function save(): void
    {
        $this->validate();
        $this->validateFile();
        $source = new Source();
        $source->name = $this->name;
        $source->content = $this->content;
        $source->user_id = Auth::id();
        $source->type= $this->value;
        $source->save();
        $source->syncTags($this->tags);
        switch ($this->value) {
            case 'file':
                $this->file = $this->file->getRealPath();
                $this->addToRessources($source);
                break;
            case 'text':
                ContentProcessEvent::dispatch($source->id, tenant()->id, $this->content);
                break;
            case 'url':
                ScrapperProcessEvent::dispatch($source->id,$this->url);
                break;
        }
        $this->dispatch('refreshDatatable');
        $this->closeModal();
        $this->notification()->success(
            $title = trans('Action saved'),
            $description = trans('Your action was successfully saved')
        );
    }

    public function addToRessources($source): void
    {
        $source->addFile($this->file)->in('tmp')->on('local')->save();
        DocumentProcessEvent::dispatch(tenant(),$source);
    }

    public function render()
    {
        return view('modal.tenant.backend.source.create');
    }
}
