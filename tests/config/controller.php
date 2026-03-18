<?php

declare(strict_types=1);

return [
    'jobs' => [
        'product' => [
            'export' => [
                'location' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp',
                'max-items' => 15,
                'max-query' => 5,
                'sitemap' => [
                    'location' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp',
                    'max-items' => 15,
                    'max-query' => 5,
                ],
            ],
        ],
        'catalog' => [
            'export' => [
                'sitemap' => [
                    'location' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp',
                    'max-items' => 10,
                    'max-query' => 5,
                ],
            ],
        ],
    ],
];
