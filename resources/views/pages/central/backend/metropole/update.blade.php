<x-organismes.section>
    <x-organismes.form title="Create a New Company" description="Use this form to create a new company by providing essential details">

        <div class="grid gap-8">
            <x-atoms.forms.input-field title="Name of the Company" description="Enter the official name of the company for identification and records.">
                <x-input sm placeholder="{{__('Metropole company')}}" wire:model.live="name" />
            </x-atoms.forms.input-field>

            <x-atoms.forms.input-field title="Institution Type" description="Select the type of institution from the available options.">
                <x-select sm placeholder='{{ __("Institution of company") }}' wire:model.live="type">
                    @foreach($governmentInstitutions as $governmentInstitution)
                        <x-select.option value="{{ $governmentInstitution }}">{{ __($governmentInstitution) }}</x-select.option>
                    @endforeach
                </x-select>
            </x-atoms.forms.input-field>

        </div>
    </x-organismes.form>
</x-organismes.section>

