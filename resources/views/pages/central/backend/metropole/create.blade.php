<x-organismes.section>
    <x-organismes.form title="Create a New Métropole" description="Use this form to create a new métropole by providing essential details">

        <div class="grid gap-8">
            <x-atoms.forms.input-field title="Name of the Metropole" description="Enter the official name of the métropole for identification and records.">
                <x-input sm placeholder="{{__('Metropole name')}}" wire:model.live="name" />
            </x-atoms.forms.input-field>

            <x-atoms.forms.input-field title="Create a new User" description="Add a new user to the métropole with the necessary details for access and management.">
                <div class="grid grid-cols-2 gap-3">
                <x-input sm placeholder="{{__('First name')}}" wire:model.live="first_name" />
                <x-input sm placeholder="{{__('Last name')}}" wire:model.live="last_name" />
                <x-input sm placeholder="{{__('Email')}}" wire:model.live="email" class="col-span-2"/>
                <x-password sm placeholder="{{__('Password')}}" wire:model.live="password" class="col-span-2"/>
                <x-password sm placeholder="{{__('Confirm Password')}}" wire:model.live="password_confirmation" class="col-span-2"/>
                </div>
            </x-atoms.forms.input-field>
        </div>
    </x-organismes.form>
</x-organismes.section>
