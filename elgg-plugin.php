<?php

/**
 * Disk quota
 * @author Nikolai Shcherbin
 * @license GNU Public License version 2
 * @copyright (c) Nikolai Shcherbin 2025
 * @link https://wzm.me
**/

return [
    'plugin' => [
        'name' => 'Disk quota',
        'version' => '4.0.0',
        'dependencies' => [
            'file' => [
                'position' => 'after',
                'must_be_active' => true,
            ],
        ],
        'activate_on_install' => true,
    ],

    'actions' => [
        'diskquota/edit' => [
            'controller' => \wZm\Files\DiskQuota\Actions\Edit::class,
            'access' => 'admin',
        ],
        'file/upload' => [
            'controller' => \wZm\Files\DiskQuota\Actions\Upload::class,
        ],
    ],

    'events' => [
        'delete' => [
            'object' => [
                \wZm\Files\DiskQuota\Events\OnDeleteObject::class => [],
            ],
        ],
        'register' => [
            'menu:entity' => [
                \wZm\Files\DiskQuota\Menus\Entity::class => [],
            ],
        ],
        'restore' => [
            'object' => [
                \wZm\Files\DiskQuota\Events\OnRestoreObject::class => [],
            ],
        ],
        'trash' => [
            'object' => [
                \wZm\Files\DiskQuota\Events\OnDeleteObject::class => [],
            ],
        ],
    ],

    'view_extensions' => [
        'elgg.css' => [
            'diskquota/style.css' => [],
        ],
        'file/sidebar' => [
            'diskquota/sidebar' => [
                'priority' => 800
            ],
        ],
    ],

    'view_options' => [
        'forms/diskquota/edit' => ['ajax' => true],
    ],

    'settings' => [
        'global_disk_space' => 0,
    ],
];
