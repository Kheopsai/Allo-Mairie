<?php

namespace App\View\Pages\Central\Backend\Metropole;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Metropole;
use App\Traits\HasActionResource;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{
    use WireUiActions;
    use HasActionResource;

    protected $model = Metropole::class;

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
        $this->sethidebulkactionswhenemptystatus(count($this->getselected()) == 0)
            ->setPrimaryKey('id')
            ->setConfigurableAreas([
                'toolbar-right-start' => 'components.atoms.columns.add',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make('Name','name'),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }

    public function add()
    {
        return redirect()->route('metropole.create');
    }
}
