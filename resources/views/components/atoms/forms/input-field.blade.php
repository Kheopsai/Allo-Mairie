<div class="grid gap-8">
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-3">
            @isset($title)
                <div class="font-semibold text-sm">
                    {{ __($title) }}
                </div>
            @endisset
            @isset($description)
                <div class="text-xs">
                    {{ __($description) }}
                </div>
            @endisset
        </div>
        <div class="col-span-6">
            {{ $slot }}
        </div>
    </div>
    <div>
        <hr>
    </div>
</div>
