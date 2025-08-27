<?php

namespace wZm\Files\DiskQuota\Events;

use Elgg\Exceptions\Http\InternalServerErrorException;

class OnRestoreObject
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

        if (!$entity->diskspace_used || !$entity->isDeleted()) {
            return;
        }

        $user = $entity->getOwnerEntity();
        if (!$user instanceof \ElggUser) {
            return;
        }

        $disk_quota = new \wZm\Files\DiskQuota\Service\Quota($user);

        if (!(bool) $disk_quota->restore($entity->getSize())) {
            throw new InternalServerErrorException(elgg_echo('diskquota:limit'));
        }

        return true;
    }
}
