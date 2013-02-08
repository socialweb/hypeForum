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

function hj_forum_register_dashboard_title_buttons($dashboard = 'site') {

	switch ($dashboard) {

		case 'site' :

			if (elgg_is_admin_logged_in()) {

				$site = elgg_get_site_entity();

				elgg_register_menu_item('title', array(
					'name' => 'create:forum',
					'text' => elgg_echo('hj:forum:create:forum'),
					'href' => "forum/create/forum/$site->guid",
					'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 100
				));

				elgg_register_menu_item('title', array(
					'name' => 'create:category',
					'text' => elgg_echo('hj:forum:create:category'),
					'href' => "forum/create/category/$site->guid",
					'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
					'data-callback' => 'newcategory::framework:forum',
					'data-toggle' => 'dialog',
					'priority' => 200
				));
			}

			break;
	}
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
