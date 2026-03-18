<?php

declare(strict_types=1);

return [
    'name' => 'ai-controller-jobs',
    'depends' => [
        'aimeos-core',
    ],
    'include' => [
        'src',
    ],
    'i18n' => [
        'controller/jobs' => 'i18n',
    ],
    'config' => [
        'config',
    ],
    'template' => [
        'controller/jobs/templates' => [
            'templates/controller/jobs',
        ],
    ],
    'custom' => [
        'controller/jobs' => [
            'src',
        ],
    ],
];
