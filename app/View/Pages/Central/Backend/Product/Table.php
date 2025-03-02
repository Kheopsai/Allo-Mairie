<?php

namespace App\View\Pages\Central\Backend\Product;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use App\Traits\HasActionResource;
use Illuminate\Support\Collection;

class Table extends DataTableComponent
{

    use HasActionResource;


    protected $model = Product::class;


    public function actions(): array
    {

        $actions = [

            auth()->user()->hasPermission('plan-update') ? $this->getDefaultEditAction() : null,
            auth()->user()->hasPermission('plan-delete') ? $this->getDefaultDeleteAction() : null,

        ];
        return array_filter($actions);
    }

    public function configure(): void
    {
        $this->setHideBulkActionsWhenEmptyStatus(count($this->getSelected()) == 0)
            ->setPrimaryKey('id')
            ->setConfigurableAreas([
                'toolbar-right-start' => 'components.atoms.columns.add',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make(trans("Name"), "name")
                ->sortable(),
            Column::make(trans("Description"), "description")
                ->sortable(),
            Column::make(trans("Price"), "id")
                ->format(function($id){
                    return Product::find($id)->price;
                })
                ->sortable(),
            Column::make(trans("Value"), "value")
                ->sortable(),
            Column::make(trans("Created at"), "created_at")
                ->sortable(),
        ];
    }

    public function add()
    {
        redirect()->route('product.create');
    }

    public function edit(Product $product)
    {
        redirect()->route('product.update',['product'=> $product]);
    }
}
