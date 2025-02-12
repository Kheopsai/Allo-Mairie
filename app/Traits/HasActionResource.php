<?php

namespace App\Traits;

use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ComponentColumn;

trait HasActionResource
{
    public function deleteConfirmation($id = null): void
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

    public function appendColumns(): array
    {
        return [
            ComponentColumn::make(trans('Actions'), 'id')
                ->component('atoms.columns.action')
                ->attributes(fn ($value, $row, Column $column) => [
                    'actions' => $this->actions(),
                    'id' => $value,
                ]),
        ];
    }

    public function confirmDelete($id): void
    {
        $selectedIds = $this->getSelected();
        if (count($selectedIds) > 0) {
            foreach ($selectedIds as $itemId) {
                $topic = $this->model::find($itemId);
                if ($topic) {
                    $topic->delete();
                }
            }
            $this->clearSelected();
            $this->notification()->error(
                $title = trans('Action status'),
                $description = trans('Action run with success')
            );
        } else {
            $topic = $this->model::find($id);
            if ($topic) {
                $topic->delete();
                $this->notification()->error(
                    $title = trans('Action status'),
                    $description = trans('Action run with success')
                );
            } else {
                $this->notification()->error(
                    $title = trans('Server Error'),
                    $description = trans('Not Found')
                );
            }
        }

        $this->dispatch('refreshDatatable');
    }
    public function bulkActions(): array
    {
        return [
            'deleteConfirmation' => trans('Delete'),
        ];
    }
}
