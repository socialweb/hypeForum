<?php

// Custom order by clauses
elgg_register_plugin_hook_handler('order_by_clause', 'framework:lists', 'hj_forum_order_by_clauses');

// Custom search clause
elgg_register_plugin_hook_handler('custom_sql_clause', 'framework:lists', 'hj_forum_filter_forum_list');

// Allow users to use forums as container entities
elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'hj_forum_container_permissions_check');

/**
 * Custom clauses for forum ordering
 */
function hj_forum_order_by_clauses($hook, $type, $options, $params) {

	$order_by = $params['order_by'];
	$direction = $params['direction'];

	list($prefix, $column) = explode('.', $order_by);

	if (!$prefix || !$column) {
		return $options;
	}

	if ($prefix !== 'forum') {
		return $options;
	}

	$prefix = sanitize_string($prefix);
	$column = sanitize_string($column);
	$direction = sanitize_string($direction);

	$dbprefix = elgg_get_config('dbprefix');

	$order_by_prev = elgg_extract('order_by', $options, false);

	switch ($column) {

		case 'topics' :
			$options = hj_framework_get_order_by_descendant_count_clauses(array('hjforum', 'hjforumtopic'), $direction, $options);

			break;

		case 'posts' :
			$options = hj_framework_get_order_by_descendant_count_clauses(array('hjforumpost'), $direction, $options);
			break;

		case 'author' :
			$options['joins'][] = "JOIN {$dbprefix}users_entity ue ON ue.guid = e.owner_guid";
			$options['order_by'] = "ue.name $direction";
			break;
	}

	if ($order_by_prev) {
		$options['order_by'] = "$order_by_prev, {$options['order_by']}";
	}

	return $options;
}

/**
 * Custom clauses for forum keyword search
 */
function hj_forum_filter_forum_list($hook, $type, $options, $params) {

	if (!is_array($options['subtypes'])) {
		if (isset($options['subtype'])) {
			$options['subtypes'] = array($options['subtype']);
			unset($options['subtype']);
		} elseif (isset($options['subtypes'])) {
			$options['subtypes'] = array($options['subtypes']);
		} else {
			return $options;
		}
	}
	
	if (!in_array('hjforum', $options['subtypes'])
			&& !in_array('hjforumtopic', $options['subtypes'])) {
		return $options;
	}
	
	$query = get_input("__q", false);

	if (!$query || empty($query)) {
		return $options;
	}

	$query = sanitise_string($query);

	$dbprefix = elgg_get_config('dbprefix');
	$options['joins'][] = "JOIN {$dbprefix}objects_entity oe_q ON e.guid = oe_q.guid";
	$options['wheres'][] = "MATCH(oe_q.title, oe_q.description) AGAINST ('$query')";

	return $options;
}

/**
 * Bypass default permission to allow users to add posts and topics to forums
 */
function hj_forum_container_permissions_check($hook, $type, $return, $params) {

	$container = elgg_extract('container', $params, false);
	$user = elgg_extract('user', $params, false);
	$subtype = elgg_extract('subtype', $params, false);

	if (!$container || !$user || !$subtype)
		return $return;

	switch ($container->getSubtype()) {

		default :
			return $return;
			break;

		case 'hjforum' :

			switch ($subtype) {

				default :
					return $return;
					break;

				case 'hjforum' : // Adding sub-forum
					return $return;
					break;

				case 'hjforumtopic' : // Adding new topics
					return ($container->isOpenFor($subtype));
					break;
			}
			break;

		case 'hjforumtopic' :
			switch ($subtype) {

				default :
					return $return;
					break;

				case 'hjforumpost' :
					return ($container->isOpenFor($subtype));
					break;
			}
			break;
	}
}