<?php

namespace App\Actions\Tenant\Backend;

use App\Facade\LlmManagerFacade;
use App\Models\Hub;
use App\Services\CategoriesExtractor\CategoriesExtractor;
use Lorisleiva\Actions\Concerns\AsAction;

class CategoriesExtraction
{
    use AsAction;

    protected CategoriesExtractor $categoriesExtractor;

    public function __construct($context = null)
    {
        $this->categoriesExtractor= new CategoriesExtractor;
    }

    public function handle($content)
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
        if(str_contains($result,'undefined'))
            return null;
        if (preg_match('/\{.*?\}/', $result, $matches))
            $jsonContent = $matches[0];
        $cateogry= json_decode($jsonContent) ;
        return $cateogry->id;
    }
}
