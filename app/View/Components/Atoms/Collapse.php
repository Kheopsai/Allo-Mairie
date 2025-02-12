<?php

namespace App\View\Components\Atoms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Collapse extends Component
{
    public string $title;

    public string $open;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $open = false)
    {
        $this->title = $title;
        if (is_array($open)) {
            $this->open = $this->isSectionOpen($open);
        } else {
            $this->open = (bool) $open;
        }
    }

    public function isSectionOpen(array $routes): bool
    {
        foreach ($routes as $route) {
            if (request()->is($route)) {
                return true;
            }
        }

        return false;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.atoms.collapse');
    }
}
