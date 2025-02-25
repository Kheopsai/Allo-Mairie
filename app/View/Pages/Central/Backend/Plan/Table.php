<?php

namespace App\View\Pages\Central\Backend\Plan;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Plan;
use App\Traits\HasActionResource;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{
    use HasActionResource;
    use WireUiActions;

    protected $model = Plan::class;

    public function actions(): array
    {
        return [
            [
                'label' => trans('Edit'),
                'icon' => 'pencil',
                'action' => 'edit',
            ],
            [
                'label' => trans('Delete'),
                'icon' => 'trash',
                'action' => 'deleteConfirmation',
            ],
        ];
    }

    public function configure(): void
    {
        $this->setHideBulkActionsWhenEmptyStatus(count($this->getSelected()) == 0)
            ->setPrimaryKey('id')
            ->setConfigurableAreas([
                'toolbar-right-start' => 'components.atoms.columns.add',
            ]);
    }

    public function add()
    {
        return redirect()->route('plan.create');
    }

    public function edit(Plan $plan)
    {
        return redirect()->route('plan.update', ['plan' => $plan]);
    }

    public function columns(): array
    {
        return [
            Column::make(trans('ID'), 'id')
                ->sortable(),
            Column::make(trans('Name'), 'name'),
            Column::make(trans('Description'), 'short_description')
                ->collapseAlways(),
            Column::make(trans('Features'), 'id')
                ->format(fn($value) => Plan::find($value)->features->pluck('name')->join(', ', ' and '))
                ->collapseAlways(),
            BooleanColumn::make(trans('Archived'), 'archived'),
            Column::make(trans('Updated at'), 'updated_at')
                ->format(fn($value) => $value ? $value->diffForHumans() : ''),
        ];
    }
}
