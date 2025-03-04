<?php

namespace App\View\Pages\Tenant\Backend\Settings\User;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\SyncedUser;
use App\Traits\HasActionResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ComponentColumn;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{
    use WireUiActions;
    use HasActionResource;

    public string $tableName = 'users';
    public function actions(): array
    {
        $actions= [
            // [
            //     'label' => trans('Reset password'),
            //     'icon' => 'lock-closed',
            //     'action' => 'resetPassword',
            // ],
            auth()->user()->HasPermission('user-delete') ? $this->getDefaultDeleteAction() : null
            // [
            //     'label' => trans('Delete'),
            //     'icon' => 'trash',
            //     'action' => 'deleteConfirmation',
            // ],

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
    public function deleteConfirmation($id): void
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

    /**
     * @throws Throwable
     */
    public function confirmDelete(SyncedUser $user): void
    {
        $user->deleteOrFail();
        tenant()->removeSeat();

        $this->notification()->error(
            $title = trans('Action status'),
            $description = trans('Action run with success')
        );
    }
    public function add(): void
    {
        $this->authorize('create',\App\Models\User::class);
        $this->dispatch('openModal', 'modal.tenant.backend.user.create');
    }

    // public function resetPassword(User $user): void
    // {
    //     $this->dispatch('openModal', 'modal.tenant.users.reset', ['user' => $user]);
    // }
    public function builder(): Builder
    {
        return SyncedUser::whereKeyNot(Auth::id());
    }
    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable(),
            Column::make(trans('Full name'), 'id')
                ->format(fn ($value) => SyncedUser::find($value)->full_name ?? '/')
                ->sortable(),
            Column::make(trans('Email'), 'email')
                ->sortable(),
            Column::make(trans('Role'), 'id')
                ->format(fn ($value) => SyncedUser::find($value)->roles->first()->name ?? '/' )
                ->sortable(),
            Column::make('Created at', 'created_at')
                ->sortable(),
            // ComponentColumn::make(trans('Actions'), 'id')
            //     ->component('atoms.columns.actions')
            //     ->attributes(fn ($value, $row, Column $column) => [
            //         'actions' => $this->actions(),
            //         'id' => $value,
            //     ]),
        ];
    }
}
