<x-organismes.section>
    <x-slot name="title">
        {{ trans('Category title').': '.$hub->name }}
    </x-slot>
    <x-slot name="description">
        {{ __('Your centralized hub for bite-sized content elements. Seamlessly integrate these snippets into your content or chatbot, enhancing experiences with ease. From essential company details to contextual pieces, everything\'s at your fingertips') }}
    </x-slot>

    <div class="new-directory">
        <livewire:pages.tenant.backend.hub.source.table :hub="$hub"/>
    </div>
</x-organismes.section>
