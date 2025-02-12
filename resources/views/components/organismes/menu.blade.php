<div>
    <div class="flex items-center flex-1 px-6 py-4">
        <a href="{{ route('dashboard') }}" title=""
           class="flex items-center space-x-2 rounded outline-none focus:ring-0">
            <div class="flex space-x-1 items-end">
                <div class="text-2xl font-semibold text-secondary-900 dark:text-secondary-100">
                    {{ config('app.name') }}
                </div>
                <div class="text-xs font-light dark:text-secondary-500">
                    AI
                </div>
            </div>
        </a>
    </div>
    @if (!tenant())
        <x-organismes.menu.central/>
    @else
        <x-organismes.menu.tenant/>
    @endif
</div>
