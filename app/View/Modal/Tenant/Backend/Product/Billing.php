<?php

namespace App\View\Modal\Tenant\Backend\Product;

use App\Enums\RoleEnum;
use App\Models\CreditRequest;
use App\Models\Product;
use App\Services\Tokens\CreditService;
use LivewireUI\Modal\ModalComponent;
use WireUi\Traits\WireUiActions;

class Billing extends ModalComponent
{
    use WireUiActions;

    public $products;

    //  protected static array $maxWidths = [
    //     'sm'  => 'sm:max-w-sm',
    //     'md'  => 'sm:max-w-md',
    //     'lg'  => 'sm:max-w-md md:max-w-lg',
    //     'xl'  => 'sm:max-w-md md:max-w-xl',
    //     '2xl' => 'sm:max-w-md md:max-w-xl lg:max-w-2xl',
    //     '3xl' => 'sm:max-w-md md:max-w-xl lg:max-w-3xl',
    //     '4xl' => 'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-4xl',
    //     '5xl' => 'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-5xl',
    //     '6xl' => 'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-5xl xl:max-w-6xl',
    //     '7xl' => 'sm:max-w-md md:max-w-xl lg:max-w-3xl xl:max-w-5xl xl:max-w-7xl',
    // ];

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
        return '4xl';
    }

    public function save($product_id)
    {
        if(auth()->user()->hasRole(RoleEnum::Admin))
        {
        $credit= new CreditService;
        $credit->addCredits(auth()->user(),Product::find($product_id)->value);
        }
        else{

            $this->authorize('create',CreditRequest::class);
        CreditRequest::create([
            'user_id'=> auth()->id(),
            'product_id'=> $product_id
        ]);
        }
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
