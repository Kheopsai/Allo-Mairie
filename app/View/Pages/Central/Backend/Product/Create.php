<?php

namespace App\View\Pages\Central\Backend\Product;

use App\Enums\CurrencyEnum;
use App\Models\Product;
use App\Services\Payments\Stripe\StripeService;
use App\Support\FormComponent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('components.templates.admin')]
class Create extends FormComponent
{

    #[Rule('required|min:3')]
    public $name;

    #[Rule('required|min:6')]
    public $description;

    #[Rule('required|numeric')]
    public $price;

    #[Rule('required|numeric')]
    public $value;


    public function save()
    {
        $stripe = new StripeService;
        $stripeproduct = $stripe->createProduct($this->name, $this->description);
        $price = $stripe->createPrice(CurrencyEnum::Eur, $this->price, null, $stripeproduct->id);
        $product = new Product;
        $product->user_id = auth()->id();
        $product->name=$this->name;
        $product->description=$this->description;
        $product->payment_id= $price->id;
        $product->sku= $stripeproduct->id;
        $product->value=$this->value;
        $product->save();

    }

    public function render()
    {
        return view('pages.central.backend.product.create');
    }
}
