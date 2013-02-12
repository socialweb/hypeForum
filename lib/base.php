<?php

function hj_forum_get_latest_topics($container_guid, $limit = 10, $count = false, $recursive = false) {

	$options = array(
		'types' => 'object',
		'subtypes' => array('hjforumtopic', 'hjforum'),
		'count' => $count,
		'limit' => $limit,
		'relationship' => 'descendant',
		'relationship_guid' => $container_guid,
		'inverse_relationship' => true,
		'order_by' => 'e.time_created DESC'
	);

	if (!$recursive) {
		$options['container_guids'] = $container_guid;
	}

	return elgg_get_entities_from_relationship($options);
}

function hj_forum_get_latest_posts($container_guid, $limit = 10, $count = false, $recursive = false) {
	$options = array(
		'types' => 'object',
		'subtypes' => 'hjforumpost',
		'count' => $count,
		'limit' => $limit,
		'relationship' => 'descendant',
		'relationship_guid' => $container_guid,
		'inverse_relationship' => true,
		'order_by' => 'e.time_created DESC'
	);

	if (!$recursive) {
		$options['container_guids'] = $container_guid;
	}

	return elgg_get_entities_from_relationship($options);
}

