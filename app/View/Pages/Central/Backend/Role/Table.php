<?php

namespace App\View\Pages\Central\Backend\Role;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Role;
use App\Traits\HasActionResource;
use Illuminate\Database\Eloquent\Builder;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{
    use WireUiActions;
    use HasActionResource;

    protected $model = Role::class;

    public function actions(): array
    {
        $actions = [

            auth()->user()->hasPermission('role-update') ? $this->getDefaultEditAction() : null,
            auth()->user()->hasPermission('role-delete') ? $this->getDefaultDeleteAction() : null,

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
            Column::make(__('ID'), 'id')
                ->searchable()
                ->sortable(),
            Column::make(__('Name'), 'name')
                ->searchable()
                ->sortable(),
            Column::make(__('Display name'), 'display_name')
                ->searchable()
                ->sortable(),
            Column::make(__('Description'), 'description')
                ->format(fn ($value) => $value ?? trans('No data found'))
                ->collapseAlways()
                ->sortable(),
            Column::make(__('Permissions'), 'permissions.name')
                ->label(fn ($row) => Str()->ucfirst($row->permissions?->pluck('name')?->implode(', ')) ?? trans('No data found'))
                ->collapseAlways(),
        ];
    }

    public function builder(): Builder
    {
        return Role::query();
    }

    public function add()
    {
        $this->authorize('create',Role::class);
        $this->dispatch('openModal', 'modal.central.role.create');
    }

    public function edit(Role $role)
    {
        $this->authorize('update',$role);
        $this->dispatch('openModal', 'modal.central.role.update', ['role' => Role::find($role)]);
    }
}
