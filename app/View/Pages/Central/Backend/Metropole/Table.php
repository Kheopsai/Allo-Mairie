<?php

namespace App\View\Pages\Central\Backend\Metropole;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Metropole;
use App\Traits\HasActionResource;
use Illuminate\Support\Facades\Gate;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{
    use WireUiActions;
    use HasActionResource;

    protected $model = Metropole::class;

    public function actions(): array
    {
        $actions= [

            auth()->user()->hasPermission('company-update') ? $this->getDefaultEditAction() : null,
            auth()->user()->hasPermission('company-delete') ? $this->getDefaultDeleteAction() : null,

        ];
        return array_filter($actions);
    }

    public function configure(): void
    {
        $this->sethidebulkactionswhenemptystatus(count($this->getselected()) == 0)
            ->setPrimaryKey('id');
           if(Gate::allows('create',Metropole::class))
            $this
            ->setConfigurableAreas([
                'toolbar-right-start' => 'components.atoms.columns.add',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make(trans('Name'),'name'),
            Column::make(trans('Type'),'type'),
            Column::make(trans("Created at"), "created_at")
                ->sortable(),
            Column::make(trans("Updated at"), "updated_at")
                ->sortable(),
        ];
    }

    public function add()
    {
        return redirect()->route('metropole.create');
    }


    public function edit(Metropole $metropole)
    {
        return redirect()->route('metropole.update',['metropole'=> $metropole]);
    }
}
