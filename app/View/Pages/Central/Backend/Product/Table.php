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
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Description", "description")
                ->sortable(),
            Column::make("Price", "id")
                ->format(function($id){
                    return Product::find($id)->price;
                })
                ->sortable(),
            Column::make("Value", "value")
                ->sortable(),
            Column::make("Created at", "created_at")
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
