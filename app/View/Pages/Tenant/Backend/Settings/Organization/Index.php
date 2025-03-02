<?php

namespace App\View\Pages\Tenant\Backend\Settings\Organization;

use App\Models\Metropole;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Throwable;
use WireUi\Traits\WireUiActions;

class Index extends Component
{
    use WireUiActions;
    use CentralConnection;
    use WithFileUploads;

    #[Rule(['required'])]
    public $name;

    public $media = [];

    public Metropole $company;


    public function mount(): void
    {
        $this->setCompany(tenant()->metropole);
    }

    public function setCompany(Metropole $company): void
    {
        $this->company = $company;
        $this->name = $company->name;
        $this->media = $company->bindToDropzone('companies');
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function save(): void
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $this->company->update($this->all());
            if (count($this->media) > 0) {
                $this->company->syncImage($this->media, 'companies');
            }
            DB::commit();
            $this->notification()->success(
                $title = trans('Action saved'),
                $description = trans('Your action was successfully saved')
            );
        } catch (Throwable $exception) {
            DB::rollBack();
            $this->notification()->error(
                $title = trans('Server Error'),
                $description = trans('Action error')
            );
        }
    }

    public function render()
    {
        return view('pages.tenant.backend.settings.organization.index');
    }
}
