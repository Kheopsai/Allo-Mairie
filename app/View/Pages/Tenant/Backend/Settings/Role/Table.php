<?php

namespace App\View\Pages\Tenant\Backend\Settings\Role;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Role;
use App\Traits\HasActionResource;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{
    use WireUiActions;
    use HasActionResource;

    protected $model = Role::class;

    public string $tableName = 'roles';
    public function actions(): array
    {
        $actions= [
            auth()->user()->hasPermission('role-update')? $this->getDefaultEditAction():null,
            auth()->user()->hasPermission('role-delete')? $this->getDefaultDeleteAction():null,

        ];
        return array_filter($actions);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setHideBulkActionsWhenEmptyStatus(count($this->getSelected()) == 0)
            ->setConfigurableAreas([
                'toolbar-right-start' => 'components.atoms.columns.add',
            ]);
    }
    public function deleteConfirmation($id)
    {
        $this->dialog()->confirm([
            'icon' => 'error',
            'title' => trans('Are you Sure?'),
            'description' => trans('Delete resource selected'),
            'acceptLabel' => trans('Yes, delete it'),
            'method' => 'confirmDelete',
            'params' => $id,
        ]);
    }
    public function confirmDelete(Role $role)
    {
        $this->authorize('delete',$role);
        $role->deleteOrFail();

        $this->notification()->error(
            $title = trans('Action status'),
            $description = trans('Action run with success')
        );
    }
    public function edit(Role $role)
    {
        $this->authorize('update',$role);
        $this->dispatch('openModal', 'modal.central.role.update', ['role' => $role]);
    }
    public function add()
    {
        $this->authorize('create',Role::class);
        $this->dispatch('openModal', 'modal.central.role.create');
    }
    public function columns(): array
    {
        return [
            Column::make(trans('Id'), 'id')
                ->sortable(),
            Column::make(trans('Name'), 'name')
                ->sortable(),
            Column::make(trans('Display name'), 'display_name')
                ->sortable(),
            Column::make(trans('Description'), 'description')
                ->sortable(),
            Column::make(trans('Created at'), 'created_at')
                ->sortable(),
        ];
    }
}
