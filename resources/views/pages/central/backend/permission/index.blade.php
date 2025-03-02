<x-organismes.section>

    <x-slot name="title" class="flex justify-between">
        {{ trans('Permissions') }}

    </x-slot>
    <x-organismes.form title="Permissions Management"
        description="Manage permissions by attaching or dettaching to roles.">

        <div>
            <hr>
        </div>
        <div>
            <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg shadow-md">
                <thead class="">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Model
                        </th>
                        <!-- Add columns for roles -->
                        @foreach ($roles as $role)
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 capitalize tracking-wider">
                                {{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Iterate over models -->
                    @foreach ($this->permissions as $modelName => $permissions)
                        <tr x-data="{ open: false }" class="">
                            <td class="border px-4 py-2">
                                <div class="col-span-9">
                                    <div class="flex items-start">
                                        <p class="px-2">{{ __(ucwords($modelName) . ' Management') }}</p>
                                        <button @click="open = !open" x-html="open ? '-' : '+'"
                                            class="text-black hover:text-gray-500 font-bold text-md"></button>
                                    </div>
                                    <div x-show="open" x-cloak class="px-8 py-4 space-y-2" x-transition>
                                        @foreach ($permissions as $permission)
                                            <div class="flex space-x-2">
                                                <p>{{ ucfirst($permission) }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <!-- Roles columns with checkboxes for assignments -->

                            @foreach ($roles as $role)
                                <td class="border px-4 py-2">
                                    <div class="col-span-9">
                                        <div>
                                            {{-- <div class="flex space-x-2">
                                                <x-checkbox wire:model.live="assignAllPermissions.{{ $role->id }}.{{ explode('-', $permission)[0] }}"  label="{{ __('assign all') }}" />
                                            </div> --}}
                                            <div x-show="open" x-cloak class="px-8 py-4 space-y-2" x-transition>
                                                @foreach ($permissions as $permission)
                                                    <div class="flex space-x-2">
                                                        <x-checkbox
                                                            wire:model.live="selectedPermissions.{{ $role->id }}.{{ $permission }}"
                                                            label="{{ __('assign') }}" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endforeach

                        </tr>
                    @endforeach


                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $this->permissions->links() }}
        </div>

    </x-organismes.form>
</x-organismes.section>
