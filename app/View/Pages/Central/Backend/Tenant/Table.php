<?php

namespace App\View\Pages\Central\Backend\Tenant;

use App\Enums\RoleEnum;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasActionResource;
use Livewire\Attributes\On;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{
    use WireUiActions;
    use HasActionResource;

    protected $model = Tenant::class;

    public function actions(): array
    {
        return [
            [
                'label' => trans('Add User'),
                'icon' => 'user',
                'action' => 'add',
            ],
        ];
    }

    public function configure(): void
    {
        $this->setHideBulkActionsWhenEmptyStatus(count($this->getSelected()) == 0)
            ->setPrimaryKey('id');
    }


    public function columns(): array
    {
        return [
            Column::make(trans('Company'), 'metropole.name')
                ->format(fn (
                    $value,
                    $row
                ) => "<div class='font-semibold'>{$value}</div><div class='text-xs'>{$row->id}</div>")
                ->html(),
            Column::make(trans('ID'), 'id')
                ->isHidden(),
            Column::make(trans('Primary domain'), 'id')
                ->format(fn ($value) => Tenant::findOrFail($value)->domains->first()->processed_name)
                ->collapseAlways()
                ->html(),
            Column::make(trans('Database'), 'id')
                ->format(fn ($value) => Tenant::findOrFail($value)->tenancy_db_name)
                ->collapseAlways()
                ->html(),
            Column::make(trans('Database created at'), 'id')
                ->format(fn ($value) => Tenant::findOrFail($value)->created_at->diffForHumans())
                ->collapseAlways()
                ->html(),
            BooleanColumn::make(trans('Status'), 'id')
                ->setSuccessValue(true),
            Column::make(trans('Updated at'), 'updated_at')
                ->format(fn ($value) => $value->diffForHumans()),
        ];
    }

    public function builder(): Builder
    {
        return Tenant::query();
    }

    public function add(Tenant $tenant): void
    {
        $this->authorize('create',User::class);
        $this->dispatch('openModal', 'modal.central.user.create', ['tenant' => $tenant]);
    }

    /**
     * @throws Throwable
     */
    #[On('delete')]
    public function delete(User $value): void
    {
        $this->authorize('delete',$value);
        $value->deleteOrFail();
    }

    protected function replicateUser($user)
    {
        $newUser = $user->replicate();
        $newUser->save();

        return $newUser;
    }

    protected function assignAdminRole($user): void
    {
        $user->addRole(RoleEnum::Admin);
    }
}
