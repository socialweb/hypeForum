<?php

if (elgg_is_xhr()) {
	$data = get_input('listdata');

	$sync = elgg_extract('sync', $data, 'old');
	$guid = elgg_extract('items', $data, 0);

	if (is_array($guid)) {
		if ($sync == 'new') {
			$guid = $guid[0];
		} else {
			$guid = end($guid);
		}
	} else {
		$guid = 0;
	}

	$last = get_entity($guid);
	$last_action = $last->last_action;

	$options = elgg_extract('options', $data, array());
	array_walk_recursive($options, 'hj_framework_decode_options_array');

	$data['pagination'] = hj_framework_decode_params_array($data['pagination']);

	$limit = elgg_extract('limit', $data['pagination'], 10);
	$offset = elgg_extract('offset', $data['pagination'], 0);

	$defaults = array(
		'offset' => $limit,
		'limit' => $offset,
		'type' => 'object',
		'subtype' => 'hjforumtopic',
		'metadata_name_value_pairs' => array(
			array('name' => 'widget', 'value' => $options['widget_guid']),
			array('name' => 'sticky', 'value' => 'false')
		),
		'class' => 'hj-syncable-list',
		'pagination' => true
	);

    if ($sync == 'new') {
        $options['wheres'] = array("e.last_action > $last_action");
        $options['order_by'] = 'e.last_action asc';
        $options['limit'] = 0;
    } else {
        $options['wheres'] = array("e.last_action < $last_action");
        $options['order_by'] = 'e.last_action desc';
    }

    $options = array_merge($defaults, $options);

    $items = elgg_get_entities_from_metadata($options);

    if (is_array($items) && count($items) > 0) {
        foreach ($items as $item) {
			if (!elgg_instanceof($item)) {
				$item = get_entity($item->guid);
			}
			$id = "elgg-{$item->getType()}-{$item->guid}";
            $html = "<li id=\"$id\" class=\"elgg-item hj-view-entity elgg-state-draggable hj-forum-topics-list\">";
            $html .= elgg_view_list_item($item, array('full_view' => $data['pagination']['full_view']));
            $html .= '</li>';

            $output[] = array('guid' => $item->guid, 'html' => $html);
        }
    }
	header('Content-Type: application/json');
    print(json_encode(array('output' => $output)));
    exit;
}

forward(REFERER);