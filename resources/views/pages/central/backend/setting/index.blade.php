<x-organismes.section>
    <x-organismes.form title="Settings">


        <div class="grid gap-8">

            <x-atoms.forms.input-field title="Platform Details"
                description="Update business information, including title, description, and SEO keywords.">
                <div class="grid gap-4">
                    <x-input sm placeholder="{{ __('Title') }}" wire:model.live="title" />
                    <x-input sm placeholder="{{ __('Description') }}" wire:model.live="description" />
                    <x-input sm placeholder="{{ __('SEO Keywords') }}" wire:model.live="seoKeywords" />
                </div>
            </x-atoms.forms.input-field>

            <x-atoms.forms.input-field title="Language" description="Select the platform's language.">
                <div class="grid gap-4">
                    <x-select sm placeholder="{{ __('languages') }}" :options="$languageEnum" wire:model="language" />
                </div>
            </x-atoms.forms.input-field>



            <x-atoms.forms.input-field title="LLM and Temperature"
                description="Usage and temperature settings of the selected LLM.">
                <div class="grid gap-4">
                    <x-select sm placeholder="{{ __('LLM') }}" :options="$modelEnum" wire:model="llm" />
                    <x-input sm placeholder="{{ __('Temperature') }}" wire:model.live="temperature" />
                </div>
            </x-atoms.forms.input-field>


            <x-atoms.forms.input-field title="Dynamic Input"
                description="Update the dynamic input for the platform." colspan="col-span-12">

                <div class="grid grid-cols-2 gap-8">

                    <x-input sm placeholder="{{ __('Openai Api Key') }}" wire:model.live="openaiApiKey" />
                    <x-input sm placeholder="{{ __('Mistral Api Key') }}" wire:model.live="mistralApiKey" />
                    <x-input sm placeholder="{{ __('Kheops Url') }}" wire:model.live="kheopsUrl" />
                    <x-input sm placeholder="{{ __('Kheops Embedding') }}" wire:model.live="kheopsEmbedding" />
                    <x-input sm placeholder="{{ __('Cashier Currency') }}" wire:model.live="cashierCurrency" />
                    <x-input sm placeholder="{{ __('Cashier Currency Locale') }}" wire:model.live="cashierCurrencyLocale" />
                    <x-input sm placeholder="{{ __('Stripe Key') }}" wire:model.live="stripeKey" />
                    <x-input sm placeholder="{{ __('Stripe Secret') }}" wire:model.live="stripeSecret" />
                    <x-input sm placeholder="{{ __('Stripe Webhook Secret') }}" wire:model.live="stripeWebhookSecret" />
                </div>
            </x-atoms.forms.input-field>

        </div>
    </x-organismes.form>
</x-organismes.section>
