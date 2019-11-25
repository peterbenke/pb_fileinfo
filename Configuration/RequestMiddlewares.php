<?php

return [
    'frontend' => [
        'peter-benke/pb-fileinfo/modify-content' => [
            'target' => \PeterBenke\PbFileinfo\Middleware\ModifyContentMiddleware::class,
            'after' => [
                'typo3/cms-frontend/maintenance-mode',
            ],
        ]
    ]
];
