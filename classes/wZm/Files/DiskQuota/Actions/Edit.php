<?php

namespace wZm\Files\DiskQuota\Actions;

class Edit
{
    /**
     * Set disk quota for user
     *
     * @param Elgg\Request $request Elgg\Request
     *
     * @return Elgg\Http\ResponseBuilder
     * @throws EntityNotFoundException
     */
    public function __invoke(\Elgg\Request $request)
    {

        $user = $request->getEntityParam('guid');

        if (!$user instanceof \ElggUser) {
            throw new \Elgg\EntityNotFoundException();
        }

        $diskquota = (int) $request->getParam('diskquota', 0);

        $user->setMetadata('disk_quota', $diskquota);

        return elgg_ok_response('', elgg_echo('diskquota:edit:success'));
    }
}
