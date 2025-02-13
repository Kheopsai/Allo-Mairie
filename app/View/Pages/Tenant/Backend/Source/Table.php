<?php

namespace App\View\Pages\Tenant\Backend\Source;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Source;
use App\Traits\HasActionResource;
use Rappasoft\LaravelLivewireTables\Views\Traits\Core\HasWireActions;

class Table extends DataTableComponent
{

    use HasWireActions;
    use HasActionResource;

    protected $model = Source::class;


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
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Type", "type")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }

    public function add()
    {
        $this->dispatch('openModal','modal.tenant.backend.source.create');
    }
}
