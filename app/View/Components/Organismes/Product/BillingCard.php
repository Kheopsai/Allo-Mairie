<?php

namespace App\View\Components\Organismes\Product;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BillingCard extends Component
{
    public Product $product;
    /**
     * Create a new component instance.
     */
    public function __construct(Product $product)
    {
        $this->product= $product;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.organismes.product.billing-card');
    }
}
