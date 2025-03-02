<?php

namespace App\View\Pages\Tenant\Backend\Settings\Appearance;

use App\Models\Setting;
use App\Traits\HasImage;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use WireUiActions;
    use WithFileUploads;

    public ?array $media = [];

    public ?string $color = '';

    public ?Setting $setting = null;

    public function boot(): void
    {
        $this->setting = tenant()->setting;
        if (! $this->setting) {
            tenant()->setting()->create();
        }
    }

    public function mount(): void
    {
        $this->color = $this->setting?->primary_color;
        $this->media = $this->setting?->bindToDropzone();

    }

    public function save(): void
    {
        $this->setting->primary_color = $this->color ?? null;
        if(!empty(($this->media)))
        $this->setting->addMedia($this->media[0]['path'])->toMediaCollection();;
        // $this->setting->syncImage($this->media);
        $this->setting->save();
        $this->notification()->success(
            $title = trans('Informations Updated'),
            $description = trans('The appearance informations were successfully updated')
        );
        $this->dispatch('colorUpdated');
    }

    public function render()
    {
        return view('pages.tenant.backend.settings.appearance.index');
    }
}
