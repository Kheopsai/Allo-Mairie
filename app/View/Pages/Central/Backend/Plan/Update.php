<?php

namespace App\View\Pages\Central\Backend\Plan;

use App\Models\Feature;
use App\Models\Plan;
use App\Support\FormComponent;
use Livewire\Attributes\Rule;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;

#[Layout('components.templates.admin')]
class Update extends FormComponent
{

    public $plan;

    #[Rule('required|min:3')]
    public $name;

    #[Rule('required|min:10')]
    public $short_description;

    #[Rule('nullable|boolean')]
    public $archived = false;

    #[Rule('nullable|boolean')]
    public $selected = false;

    public $inputname;

    public Collection $options;

    public Collection $option;

    public $inputs;

    public $number;

    public $action;

    public function addForm(): void
    {
        $rules = [
            'inputname' => 'required|min:3',
            'action' => 'required|string',
            'number' => 'required|numeric',
        ];

        $this->validate($rules);

        $input = new \stdClass;
        $input->name = $this->inputname;
        $input->action = $this->action;
        $input->number = $this->number;

        $this->inputs->push($input);
        $this->resetForm();
        $this->reset('action', 'number', 'inputname');
    }

    public function updateInputsOrder($items): void
    {
        $sorted = collect($items)
            ->sortBy('order')
            ->map(function ($order) {
                return $this->inputs[(int) $order['value']];
            })
            ->values()
            ->all();
        $this->inputs = collect($sorted);
    }

    public function addOption(): void
    {
        $this->option->push($this->option->count() + 1);
        $this->options->put($this->options->count() + 1, null);
    }

    public function deleteInput($id): void
    {
        $this->inputs->forget($id);
    }

    public function mount(Plan $plan): void
    {
        $this->plan=$plan;
        $this->name = $this->plan->name;
        $this->short_description = $this->plan->short_description;
        $this->archived = $this->plan->archived;
        $this->selected = $this->plan->selected;
        $this->inputs = $this->plan->features()->get();

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->option = collect();
        $this->options = collect();
    }

    public function save(): void
    {
        // $this->validate();
        // $stripe = new StripeService;
        // $product = $stripe->createProduct($this->name, $this->short_description);
        // $monthly_price = $stripe->createPrice(CurrencyEnum::Eur, $this->monthly_price, IntervalEnum::Month, $product->id);
        // $yearly_price = $stripe->createPrice(CurrencyEnum::Eur, $this->yearly_price, IntervalEnum::Year, $product->id);
        $plan = $this->plan;
        $plan->name = $this->name;
        $plan->short_description = $this->short_description;
        $plan->archived = $this->archived;
        $plan->selected = $this->selected;
        $plan->save();
        foreach ($this->inputs as $inputData) {
            $feature = new Feature;
            $feature->name = $inputData->name;
            $feature->action = $inputData->action;
            $feature->number = $inputData->number;
            $feature->plan_id = $plan->id;
            $feature->save();
        }
        $this->resetExcept('inputs');
        $this->inputs = collect();
        // $this->notification()->success(
        //     $title = trans('Action saved'),
        //     $description = trans('Your action was successfully saved')
        // );
    }
    public function render()
    {
        return view('pages.central.backend.plan.update');
    }
}
