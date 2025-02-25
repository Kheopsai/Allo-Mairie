<?php

namespace App\Actions\Tenant\Backend;

use App\Facade\LlmManagerFacade;
use App\Models\Hub;
use App\Services\CategoriesExtractor\CategoriesExtractor;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class CategoriesExtraction
{
    use AsAction;

    protected CategoriesExtractor $categoriesExtractor;

    public function __construct($context = null)
    {
        $this->categoriesExtractor = new CategoriesExtractor;
    }

    public function handle($content,$chat=false)
    {

        $data = Hub::select('id', 'name')->get();
        $result = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        });
        $jsonstring = json_encode($result);
        $prompt = $this->categoriesExtractor->handle($content, $jsonstring);
        $response = LlmManagerFacade::build(config('llm.config'));
        $result = $response->getResponse($prompt);
        if (preg_match('/\{.*?\}/', $result, $matches))
            $jsonContent = $matches[0];
        $category = json_decode($jsonContent);
        if ( $category->id ==="new") {
            if($chat)
            return 0;
            $newCategory = Hub::create(['name' => $category->name, 'user_id' => Auth::id()]);
            return $newCategory->id;
        } else
            return $category?->id;
    }
}
