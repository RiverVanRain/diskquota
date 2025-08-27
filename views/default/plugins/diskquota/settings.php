<?php

/**
 * Disk quota
 * @author Nikolai Shcherbin
 * @license GNU Public License version 2
 * @copyright (c) Nikolai Shcherbin 2025
 * @link https://wzm.me
**/

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
    '#type' => 'fieldset',
    'fields' => [
        [
            '#type' => 'number',
            '#label' => elgg_echo('diskquota:settings:global_disk_space'),
            '#help' => elgg_echo('diskquota:settings:global_disk_space:help'),
            'name' => 'params[global_disk_space]',
            'value' => (int) $entity->global_disk_space ?: 100,
            'min' => 0,
            'step' => 0.1,
        ],
    ],
]);
