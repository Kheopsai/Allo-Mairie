<?php

namespace App\View\Modal\Tenant\Backend\Product;

use App\Models\Product;
use App\Services\Tokens\CreditService;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;

class Billing extends ModalComponent
{
    use WireUiActions;

    public $products;

    public function mount()
    {
        $this->products= Product::all();
    }

    public function forceCloseModal()
    {
        $this->forceClose()->closeModal();
    }

    public static function modalMaxWidth(): string
    {
        return '7xl';
    }

    public function save($product_id)
    {
        $credit= new CreditService;
        $credit->addCredits(auth()->user(),Product::find($product_id)->value);
        $this->forceCloseModal();
        $this->notification()->success(
            $title = trans('Action saved'),
            $description = trans('Your action was successfully saved')
        );
    }

    public function render()
    {
        return view('modal.tenant.backend.product.billing');
    }
}
