<?php

return [
    'providers' => [
        // 'elasticsearch' => App\Services\VectorStores\ElasticSearch\ElasticSearchVectorStore::class,
        'postgres' => App\Services\VectorStores\PostgresVectorStore::class,
    ],
];
