<?php

namespace App\View\Components\Atoms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class Input extends Component
{
    public bool $borderless;

    public bool $shadowless;

    public ?string $label;

    public ?string $hint;

    public ?string $cornerHint;

    public ?string $icon;

    public ?string $rightIcon;

    public ?string $prefix;

    public ?string $suffix;

    public ?string $prepend;

    public ?string $append;

    public ?string $size;

    public bool $errorless;

    public bool $disabled;

    /**
     * Create a new component instance.
     */
    public function __construct(
        bool $borderless = false,
        bool $shadowless = false,
        ?string $label = null,
        ?string $hint = null,
        ?string $cornerHint = null,
        ?string $icon = null,
        ?string $rightIcon = null,
        ?string $prefix = null,
        ?string $suffix = null,
        ?string $prepend = null,
        ?string $append = null,
        ?string $size = null,
        bool $errorless = false,
        bool $disabled = false
    ) {
        $this->borderless = $borderless;
        $this->shadowless = $shadowless;
        $this->label = $label;
        $this->hint = $hint;
        $this->cornerHint = $cornerHint;
        $this->icon = $icon;
        $this->rightIcon = $rightIcon;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->prepend = $prepend;
        $this->append = $append;
        $this->errorless = $errorless;
        $this->size = $size;
        $this->disabled = $disabled;
    }






    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.atoms.input');
    }
}
