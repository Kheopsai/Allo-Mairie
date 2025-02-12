<?php

namespace App\View\Components\Atoms;

use App\Enums\IconEnum;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public ?string $style = null,
        public ?string $collection = null,
        public bool $solid = false,
        public bool $outline = false,
    )
    {
         $this->collection = is_null($this->collection) ? IconEnum::Heroicon : IconEnum::Lucide;
        $this->style = $this->getStyle();
    }

    private function getStyle(): ?string
    {
        if (IconEnum::hasStyle($this->collection)) {
            if ($this->solid) {
                return 's';
            }

            if ($this->outline) {
                return 'o';
            }

            return 'o';
        }
        return null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.atoms.icon');
    }
}
