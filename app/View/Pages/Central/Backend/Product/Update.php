<?php

namespace App\View\Pages\Central\Backend\Product;

use App\Enums\CurrencyEnum;
use App\Models\Product;
use App\Services\Payments\Stripe\StripeService;
use App\Support\FormComponent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.templates.admin')]
class Update extends FormComponent
{

    #[Rule('required|min:3')]
    public $name;

    #[Rule('required|min:6')]
    public $description;

    #[Rule('required|numeric')]
    public $price;

    #[Rule('required|numeric')]
    public $value;

    public Product $product;

    public function mount()
    {
        $this->name=$this->product->name;
        $this->description=$this->product->description;
        $this->price=$this->product->price;
        $this->value=$this->product->value;
    }


    public function save()
    {
        $stripe = new StripeService;
        $stripeproduct = $stripe->updateProduct($this->product->sku, $this->name, $this->description);
        // $price = $stripe->updatePrice($this->product->payment_id,CurrencyEnum::Eur, $this->price, null, $stripeproduct->id);
        $this->product->user_id = auth()->id();
        $this->product->name=$this->name;
        $this->product->description=$this->description;
        // $this->product->payment_id= $price->id;
        $this->product->sku= $stripeproduct->id;
        $this->product->value=$this->value;
        $this->product->save();

    }
    public function render()
    {
        return view('pages.central.backend.product.update');
    }
}
