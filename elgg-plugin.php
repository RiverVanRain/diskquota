<?php

/**
 * Disk quota
 * @author Nikolai Shcherbin
 * @license GNU Public License version 2
 * @copyright (c) Nikolai Shcherbin 2025
 * @link https://wzm.me
**/

return [
    'actions' => [
        'diskquota/edit' => [
            'controller' => \wZm\Files\DiskQuota\Actions\Edit::class,
            'access' => 'admin',
        ],
        'file/upload' => [
            'controller' => \wZm\Files\DiskQuota\Actions\Upload::class,
        ],
    ],

    'bootstrap' => \wZm\Files\DiskQuota\Bootstrap::class,

    'view_extensions' => [
        'elgg.css' => [
            'diskquota/style.css' => [],
        ],
    ],

    'settings' => [
        'global_disk_space' => 100,
    ],
];
