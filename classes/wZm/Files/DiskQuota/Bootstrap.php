<?php

/**
 * Disk quota
 * @author Nikolai Shcherbin
 * @license GNU Public License version 2
 * @copyright (c) Nikolai Shcherbin 2025
 * @link https://wzm.me
**/

namespace wZm\Files\DiskQuota;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap
{
    public function load()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->elgg()->events->registerHandler('delete', 'object', \wZm\Files\DiskQuota\Events\OnDeleteObject::class);

        $this->elgg()->hooks->registerHandler('register', 'menu:entity', \wZm\Files\DiskQuota\Menus\Entity::class);

        elgg_register_ajax_view('forms/diskquota/edit');

        elgg_extend_view('file/sidebar', 'diskquota/sidebar');
    }

    /**
     * {@inheritdoc}
     */
    public function ready()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function activate()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade()
    {
    }
}
