<?php

function hj_forum_get_latest_topics($container_guid, $limit = 10, $count = false, $recursive = false) {
	$options = array(
		'types' => 'object',
		'subtypes' => array('hjforumtopic'),
		'count' => $count,
		'limit' => $limit,
		'order_by' => 'e.time_created DESC'
	);

	if (!$recursive) {
		$options['container_guids'] = $container_guid;
	} else {
		$dbprefix = elgg_get_config('dbprefix');
		$options['joins'][] = "JOIN {$dbprefix}metadata topicbcmd ON topicbcmd.entity_guid = e.guid";
		$options['joins'][] = "JOIN {$dbprefix}metastrings topicbcmsn ON (topicbcmd.name_id = topicbcmsn.id)";
		$options['joins'][] = "JOIN {$dbprefix}metastrings topicbcmsv ON (topicbcmd.value_id = topicbcmsv.id)";
		$options['wheres'][] = "(topicbcmsn.string = 'breadcrumbs' AND topicbcmsv.string LIKE CONCAT('%\"', $container_guid, '\"%'))";
		$options['wheres'][] = "(e.guid != $container_guid)";
	}

	return elgg_get_entities($options);
}

function hj_forum_get_latest_posts($container_guid, $limit = 10, $count = false, $recursive = false) {
	$options = array(
		'types' => 'object',
		'subtypes' => array('hjforumpost'),
		'count' => $count,
		'limit' => $limit,
		'order_by' => 'e.time_created DESC'
	);

	if (!$recursive) {
		$options['container_guids'] = $container_guid;
	} else {
		$dbprefix = elgg_get_config('dbprefix');
		$options['joins'][] = "JOIN {$dbprefix}metadata postbcmd ON postbcmd.entity_guid = e.guid";
		$options['joins'][] = "JOIN {$dbprefix}metastrings postbcmsn ON (postbcmd.name_id = postbcmsn.id)";
		$options['joins'][] = "JOIN {$dbprefix}metastrings postbcmsv ON (postbcmd.value_id = postbcmsv.id)";
		$options['wheres'][] = "(postbcmsn.string = 'breadcrumbs' AND postbcmsv.string LIKE CONCAT('%\"', $container_guid, '\"%'))";
		$options['wheres'][] = "e.guid != $container_guid";
	}

	return elgg_get_entities($options);
}

function hj_forum_register_dashboard_title_buttons($dashboard = 'site') {

	switch ($dashboard) {

		case 'site' :

			if (elgg_is_admin_logged_in()) {

				$site = elgg_get_site_entity();

				elgg_register_menu_item('title', array(
					'name' => 'create:forum',
					'text' => elgg_echo('hj:forum:create:forum'),
					'href' => "forum/create/forum/$site->guid",
					'class' => 'elgg-button elgg-button-action',
					'data-toggle' => 'fancybox',
					'priority' => 100
				));

				elgg_register_menu_item('title', array(
					'name' => 'create:category',
					'text' => elgg_echo('hj:forum:create:category'),
					'href' => "forum/create/category/$site->guid",
					'class' => 'elgg-button elgg-button-action',
					'data-toggle' => 'fancybox',
					'priority' => 200
				));
			}

			break;
	}
}

function hj_forum_count_forums() {
	$count = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'hjforumtopic',
		'count' => true
			));
	return $count;
}

/**
 * Get a forum stack from top_level forum to the current forum
 * @param int $guid 
 */
function hj_forum_get_hierarchy_stack($guid) {

	$forum = get_entity($guid);
	if (!$forum)
		return false;

	$stack = array();
	while (elgg_instanceof($forum, 'object', 'hjforumtopic')) {
		array_unshift($stack, $forum);
		$forum = $forum->getContainerEntity();
	}

	return $stack;
}

/**
 * Push breadcrumbs for the current forum
 * @param int $guid
 */
function hj_forum_push_breadcrumbs($guid) {

	$stack = hj_forum_get_hierarchy_stack($guid);

	foreach ($stack as $f) {
		elgg_push_breadcrumb(elgg_get_excerpt($f->title, 50), $f->getURL());
	}

	return true;
}

function hj_forum_register_forum_menu_items($guid, $menu_name = 'title') {

	$forum = get_entity($guid);
	if (!$forum)
		return false;

	// Start a Thread
	// Reply | Quote & Reply
	// Edit
	// Delete

	if ($forum->canAdminister()) {

		elgg_register_menu_item($menu_name, array(
			'name' => 'add_category',
			'text' => elgg_echo('hj:forum:menu:add_category'),
			'href' => "forum/add/$forum->guid/category",
			'class' => 'elgg-button elgg-button-action',
			'section' => 'admin',
			'priority' => 100
		));

		elgg_register_menu_item($menu_name, array(
			'name' => 'edit',
			'text' => elgg_echo('hj:forum:menu:edit'),
			'href' => "forum/edit/$forum->guid",
			'class' => 'elgg-button elgg-button-action',
			'section' => 'admin',
			'priority' => 800
		));

		elgg_register_menu_item($menu_name, array(
			'name' => 'delete',
			'text' => elgg_echo('hj:forum:menu:delete'),
			'href' => "action/framework/delete/$forum->guid",
			'confirm' => true,
			'class' => 'elgg-button elgg-button-delete',
			'section' => 'admin',
			'priority' => 900
		));
	}

	if ($forum->canPost()) {
		if ($forum->children == 'forumtopic') {
			elgg_register_menu_item($menu_name, array(
				'name' => 'add_topic',
				'text' => elgg_echo('hj:forum:menu:add_topic'),
				'href' => "forum/add/$forum->guid/topic",
				'class' => 'elgg-button elgg-button-action',
				'section' => 'user',
				'priority' => 100
			));
		} else {
			elgg_register_menu_item($menu_name, array(
				'name' => 'add_reply',
				'text' => elgg_echo('hj:forum:menu:add_reply'),
				'href' => "forum/add/$forum->guid/reply",
				'class' => 'elgg-button elgg-button-action',
				'section' => 'user',
				'priority' => 200
			));
			elgg_register_menu_item($menu_name, array(
				'name' => 'add_reply_quote',
				'text' => elgg_echo('hj:forum:menu:add_reply_quote'),
				'href' => "forum/add/$forum->guid/reply?quote=true",
				'class' => 'elgg-button elgg-button-action',
				'section' => 'user',
				'priority' => 300
			));
		}
	}
}

/**
 * Get available forum instances
 * @param type $container_guid
 */
function hj_forum_get_forum_instances($container_guid = null) {

	$instances = elgg_get_metadata(array(
		'types' => 'object',
		'subtypes' => 'hjforumtopic',
		'metadata_names' => 'instance',
		'container_guids' => $container_guid,
		'group_by' => 'value_id',
		'order_by' => 'time_created ASC'
			));

	if (!$instances) {
		return false;
	}

	foreach ($instances as $instance) {
		$return[] = $instance->value;
	}

	return $return;
}

function hj_forum_find_site_forums($instance = 'mainforum') {
	$metadata = array(
		array(
			'name' => 'instance',
			'value' => $instance
			));
	$forums = hj_framework_get_entities_from_metadata_by_priority('object', 'hjforumtopic', null, elgg_get_site_entity()->guid, $metadata);
	return $forums;
}

function hj_forum_get_forum_status_options() {
	$user = elgg_get_logged_in_user_entity();
	$options['open'] = elgg_echo('hj:forum:topic:open');

	if (elgg_is_admin_logged_in() || $user->forum_admin || $user->forum_moderator) {
		$options['closed'] = elgg_echo('hj:forum:topic:closed');
	}
	$options = elgg_trigger_plugin_hook('hj:forum:status', 'all', null, $options);

	return $options;
}

function hj_forum_get_forum_children_options() {
	$user = elgg_get_logged_in_user_entity();

	$options['forumpost'] = elgg_echo('hj:forum:children:forumpost');

	if (elgg_is_admin_logged_in() || $user->forum_admin || $user->forum_moderator) {
		$options['forumtopic'] = elgg_echo('hj:forum:children:forumtopic');
	}

	$options = elgg_trigger_plugin_hook('hj:forum:children', 'all', null, $options);

	return $options;
}

function hj_forum_get_forum_sticky_options() {
	$user = elgg_get_logged_in_user_entity();

	$options['false'] = elgg_echo('hj:forum:sticky:false');

	if (elgg_is_admin_logged_in() || $user->forum_admin || $user->forum_moderator) {
		$options['true'] = elgg_echo('hj:forum:sticky:true');
	}

	$options = elgg_trigger_plugin_hook('hj:forum:sticky', 'all', null, $options);

	return $options;
}

function hj_forum_get_forum_icons() {
	$options = array('default', 'star', 'heart', 'question', 'important', 'info', 'idea', 'laugh', 'surprise', 'lightning', 'announcement', 'lock');

	$options = elgg_trigger_plugin_hook('hj:forum:icons', 'all', null, $options);

	foreach ($options as $option) {
		$label = elgg_view_icon("forum-$option");
		$options_values["$label"] = $option;
	}

	return $options_values;
}

function hj_forum_get_post_subject() {
	$extract = hj_framework_extract_params_from_url();
	$params = elgg_extract('params', $extract, array());

	$container = $params['container'];

	return elgg_echo('hj:forum:reply:prefix', array($container->title));
}
