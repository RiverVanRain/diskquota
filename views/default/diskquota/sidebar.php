<?php

if (!elgg_is_logged_in() || elgg_is_admin_logged_in()) {
    return;
}

$diskquota = new \wZm\Files\DiskQuota\Service\Quota(elgg_get_logged_in_user_entity());

$mb = $diskquota->getUsedSpaceMB();
$percent = $diskquota->getUsedSpacePercent();
$space = $diskquota->getDiskquotaMB();

$class = ($percent > 80 || $percent < 0) ? 'diskquota-state-danger' : 'diskquota-state-success';

$inner_wrapper = elgg_format_element('div', ['class' => "diskquota-inner diskquota-state {$class}", 'style' => 'width:' . $percent . '%;']);

$outer_wrapper = elgg_format_element('div', ['class' => 'diskquota-outer'], elgg_echo('diskquota:status', [
    $mb,
    $percent,
    $space
])  . $inner_wrapper);

echo elgg_format_element('div', ['class' => 'diskquota-status'], $outer_wrapper);
