<?php

namespace App\View\Components\Organismes;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public $title;
    public $description;
    /**
     * Create a new component instance.
     */
    public function __construct(?string $title,?string $description)
    {
        $this->title=$title;
        $this->description=$description;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.organismes.form');
    }
}
