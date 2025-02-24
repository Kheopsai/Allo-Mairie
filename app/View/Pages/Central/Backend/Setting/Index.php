<?php

namespace App\View\Pages\Central\Backend\Setting;

use App\Enums\LanguageEnum;
use App\Enums\ModelEnum;
use App\Models\Setting;
use App\Support\FormComponent;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;

#[Layout('components.templates.admin')]
class Index extends FormComponent
{

    public $title;
    public $description;
    public $seoKeywords;
    public $logo;
    public $temperature;
    public $language;
    public $llm;
    public $languageEnum;
    public $modelEnum;
    public $openaiApiKey;
    public $mistralApiKey;
    public $kheopsUrl;
    public $kheopsEmbedding;
    public $cashierCurrency;
    public $cashierCurrencyLocale;
    public $stripeKey;
    public $stripeSecret;
    public $stripeWebhookSecret;

    public function mount(): void
    {
        $this->languageEnum = Arr::map(LanguageEnum::asArray(), function (string $value, string $key) {
            return ucfirst($value);
        });
        $this->modelEnum = Arr::map(ModelEnum::asArray(), function (string $value, string $key) {
            return ucfirst($value);
        });
    }

    public function rules()
    {
         return [
            'title' => 'string',
            'description' => 'nullable|string',
            'seoKeywords' => 'nullable|string',
            'temperature' => 'numeric|min:0|max:1',
            'language' => 'string',
            'llm' => 'string',
            'mistralApiKey' => 'string',
            'openaiApiKey' => 'string',
            'kheopsUrl' => 'url',
            'kheopsEmbedding' => 'url',
            'cashierCurrency' => 'string',
            'cashierCurrencyLocale' => 'string',
            'stripeKey' => 'string',
            'stripeSecret' => 'string',
            'stripeWebhookSecret' => 'string',
        ];
    }

    public function save()
    {
        $this->validate();
        Setting::create($this->except('languageEnum','modelEnum'));
    }

    public function render()
    {
        return view('pages.central.backend.setting.index');
    }
}
