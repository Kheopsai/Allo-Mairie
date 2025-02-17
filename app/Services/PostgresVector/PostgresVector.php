<?php

namespace App\Services\PostgresVector;

use App\Interface\VectorStorageInterface;
use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Distance;

class PostgresVector implements VectorStorageInterface
{
    private Model $model;
    /**
     * Create a new class instance.
     */
    public function __construct($model)
    {
        $this->model= $model;
    }

    public function searchWithEmbedding(array $embedding):array
    {
        $neighborsQuery= $this->model->vectorStores()->getQuery()->nearestNeighbors('embedding',$embedding,Distance::Cosine)->get();
        return $neighborsQuery->pluck('text')->toArray();
    }

    public function save($params):void
    {
        $this->model->vectorStores()->save($params);
    }
}
