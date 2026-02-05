<?php

/*
 * PbFileinfo
 */

use PeterBenke\PbFileinfo\Middleware\ModifyContentMiddleware;

return [
    'frontend' => [
        'peter-benke/pb-fileinfo/modify-content' => [
            'target' => ModifyContentMiddleware::class,
            'after' => [
                'typo3/cms-frontend/maintenance-mode',
            ],
        ]
    ]
];
