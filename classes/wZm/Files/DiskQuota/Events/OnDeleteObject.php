<?php

namespace wZm\Files\DiskQuota\Events;

class OnDeleteObject
{
    public function __invoke(\Elgg\Event $event)
    {
        if ((int) elgg_get_plugin_setting('global_disk_space', 'diskquota') === 0) {
            return true;
        }

        if (elgg_is_admin_logged_in()) {
            return true;
        }

        $entity = $event->getObject();

        if (!$entity instanceof \ElggFile) {
            return;
        }

        $user = $entity->getOwnerEntity();
        if (!$user instanceof \ElggUser) {
            return;
        }

        $disk_quota = new \wZm\Files\DiskQuota\Service\Quota($user);

        $disk_quota->refresh($entity);

        return true;
    }
}
