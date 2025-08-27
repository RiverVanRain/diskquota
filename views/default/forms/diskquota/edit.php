<?php

if (!elgg_is_admin_logged_in()) {
    return;
}

$entity = get_entity((int) get_input('guid'));
if (!$entity instanceof \ElggUser) {
    return;
}

if ($entity->isAdmin()) {
    return;
}

$diskspace = new \wZm\Files\DiskQuota\Service\Quota($entity);

$fields = [
    [
        '#type' => 'number',
        '#label' => elgg_echo('diskquota:edit'),
        'name' => 'diskquota',
        'value' => $diskspace->getDiskquotaMB(),
        'min' => 0,
        'step' => 0.1,
    ],
    [
        '#type' => 'hidden',
        'name' => 'guid',
        'value' => (int) $entity->guid,
    ],
];

foreach ($fields as $field) {
    echo elgg_view_field($field);
}

//Submit
$submit = elgg_view('input/submit', [
    'text' => elgg_echo('save'),
    'class' => 'elgg-button elgg-button-submit',
]);

elgg_set_form_footer($submit);
