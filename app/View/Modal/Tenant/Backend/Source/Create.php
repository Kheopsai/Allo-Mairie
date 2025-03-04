<?php

namespace App\View\Modal\Tenant\Backend\Source;

use App\Actions\Tenant\Backend\Tags;
use App\Enums\SourceEnum;
use App\Events\ContentProcessEvent;
use App\Events\Documents\DocumentProcessEvent;
use App\Events\ScrapperProcessEvent;
use App\Facade\LlmManagerFacade;
use App\Jobs\Sources\ProcessDocumentJob;
use App\Models\Hub;
use App\Models\Source;
use App\Responses\HuggingFace\HuggingFaceResponse;
use App\Services\Documents\TextExtractor;
use App\Services\Extractors\SummaryExtractor;
use AssistedMindfulness\Rake\Rake;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Str;

class Create extends ModalComponent
{

    use WireUiActions;
    use WithFileUploads;

    public $hub;

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


    #[Validate('sometimes', 'url')]
    public $url;

    public function boot(): void
    {
        $this->summaryExractor = new SummaryExtractor;
        $this->textExtractor = new TextExtractor;
        $this->tagExtractor = new Tags;
    }

    public function mount(?Hub $hub)
    {
        $this->hub = $hub;
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
        // $this->validateFile();
    }

    public function loadContent(): void
    {
        $this->loadContent = ! $this->loadContent;
    }


    public function validateFile($file): void
    {
        $validator = Validator::make(
            ['file' => $file],
            [
                'file' => [
                    'required',
                    'file',
                    'mimes:pdf,doc,docx,txt,pptx,xls,xlsx',
                ],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->addError('file', $error);
            }
        }
        // if ($this->value == "file") {
        //     $this->validate([
        //         'file' => ['required', 'file', "mimes:pdf,doc,docx,txt,pptx,xls,xlsx"],
        //     ]);
        // }
    }
    public function validateUrl(): void
    {
        if ($this->value == "url") {
            $this->validate(['url' => 'required|url']);
        }
    }


    public function estimateTokenCount($text): float
    {
        return ceil(strlen($text) / 2);
    }

    public function temporaryFile(): UploadedFile
    {
        return new UploadedFile(
            $this->file,
            File::name($this->file),
            File::mimeType($this->file),
            null,
            true
        );
    }



    /**
     * @throws Exception
     */
    public function getResponse($prompt, $token = 300): string
    {
        $response =  LlmManagerFacade::build('mistral');

        return $response->getGeneratedText();
    }

    public function save(): void
    {
        $this->validate();
        if ($this->value == 'file') {
            $file= $this->temporaryFile();
            $this->validateFile($file);
        }

        $this->validateUrl();
        $source = new Source();
        $source->name = $this->name;
        $source->content = $this->content;
        $source->user_id = Auth::id();
        if ($this->hub)
            $source->hub_id = $this->hub->id;
        $source->type = $this->value;
        $source->save();
        $source->syncTags($this->tags);
        switch ($this->value) {
            case 'file':
                // $this->file = $this->file->getRealPath();
                $this->addToRessources($source,$file->path());
                break;
            case 'text':
                ContentProcessEvent::dispatch($source->id, tenant()->id, $this->content, Auth::id());
                break;
            case 'url':
                ScrapperProcessEvent::dispatch($source->id, $this->url, Auth::id());
                break;
        }
        $this->dispatch('refreshDatatable');
        $this->closeModal();
        $this->notification()->success(
            $title = trans('Action saved'),
            $description = trans('Your action was successfully saved')
        );
    }

    public function addToRessources($source,$file): void
    {
        $source->addFile($file)->in('tmp')->on('local')->save();
        // ds($source->file);
        DocumentProcessEvent::dispatch(tenant(), $source, Auth::id());
    }

    public function render()
    {
        return view('modal.tenant.backend.source.create');
    }
}
