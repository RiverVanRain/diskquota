<?php

namespace wZm\Files\DiskQuota\Menus;

class Entity
{
    public function __invoke(\Elgg\Event $event): ?\Elgg\Menu\MenuItems
    {
        if (!elgg_is_admin_logged_in()) {
            return null;
        }

        $menu = $event->getValue();

        $entity = $event->getEntityParam();
        if (!$entity instanceof \ElggUser) {
            return null;
        }

        if ($entity->isAdmin()) {
            return null;
        }

        $menu->add(\ElggMenuItem::factory([
            'name' => 'diskquota',
            'text' => elgg_echo('diskquota:user'),
            'href' => elgg_http_add_url_query_elements('ajax/form/diskquota/edit', [
                'guid' => (int) $entity->guid,
            ]),
            'link_class' => 'elgg-lightbox',
            'data-colorbox-opts' => json_encode([
                'width' => '1000px',
                'height' => '98%',
                'maxWidth' => '98%',
            ]),
            'deps' => ['elgg/lightbox'],
            'icon' => 'server',
        ]));

        return $menu;
    }
}
