<?php

namespace App\View\Pages\Tenant\Backend\CreditRequest;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\CreditRequest;
use App\Models\Product;
use App\Models\SyncedUser;
use App\Services\Tokens\CreditService;

class Table extends DataTableComponent
{
    protected $model = CreditRequest::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function save(CreditRequest $creditRequest)
    {

        $this->authorize('update',$creditRequest);
        tenant()->checkout();
        $credit= new CreditService;
        $credit->addCredits(SyncedUser::find($creditRequest['user_id']),Product::find($creditRequest['product_id'])->value);
        $creditRequest->update(['validated_at'=> now()]);
    }

    public function columns(): array
    {
        return [
            Column::make("User", "user_id")
                ->format(fn($id) => SyncedUser::find($id)->full_name)
                ->sortable(),
            Column::make("Product", "product_id")
                ->format(fn($id) => Product::find($id)->name)
                ->sortable(),
            Column::make("Product price", "product_id")
                ->format(fn($id) => Product::find($id)->price)
                ->sortable(),
            Column::make("Product credits", "product_id")
                ->format(fn($id) => Product::find($id)->value)
                ->sortable(),
            Column::make("Validated at", "id")
            ->format(function ($id){
                $creditRequest= CreditRequest::find($id);
                if($creditRequest->validated_at)
                    return $creditRequest->validated_at;
                return view('components.atoms.columns.validate',['action'=> 'save','parameters'=> $creditRequest]);
            })
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
        ];
    }
}
