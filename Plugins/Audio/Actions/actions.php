<?php

namespace Plugins\Audio\Actions;

// use Plugins\Audio\Models\Audio;

$actions = [
    'audio' => [
        'permissions' => [
        ],
        'allowedFilters' => [
        ],
        'defaultSorts' => ['-id'],
        'allowedSorts' => ['id', 'status'],
        'onlyActions' => [],
        'requests' => [ 
            // 'cut' => CutAudioAction::class,
        ],
        'actions' => [
            'cut' => SplitAudioAction::class,
        ],
    ],
];

return $actions;
