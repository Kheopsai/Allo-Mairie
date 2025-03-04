<?php

namespace App\View\Pages\Central\Backend\User;

use App\Enums\RoleEnum;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use App\Traits\HasActionResource;
use Illuminate\Database\Eloquent\Builder;
use WireUi\Traits\WireUiActions;

class Table extends DataTableComponent
{


    protected $model = User::class;


    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make(trans("Email"), "email")
                ->sortable(),
            Column::make(trans("Full name"), "id")
                ->format(fn($id)=> User::find($id)->full_name)
                ->sortable(),
            Column::make(trans("Created at"), "created_at")
                ->sortable(),
            Column::make(trans("Updated at"), "updated_at")
                ->sortable(),
        ];
    }

    public function builder(): Builder
    {
        return User::whereNot('email','admin@admin.site');
    }
}
