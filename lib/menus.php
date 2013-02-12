<?php

// Site menu
elgg_register_menu_item('site', array(
	'name' => 'forum',
	'text' => elgg_echo('forums'),
	'href' => 'forum',
));

elgg_register_plugin_hook_handler('register', 'menu:hjentityhead', 'hj_forum_entity_menu');
elgg_register_plugin_hook_handler('register', 'menu:title', 'hj_forum_entity_title_menu');

/**
 * Forum menus
 */
function hj_forum_entity_menu($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, false);

	if (!$entity instanceof hjObject)
		return $return;

	switch ($entity->getSubtype()) {

		case 'hjforum' :

			if ($entity->canWriteToContainer(0, 'object', 'hjforum') && HYPEFORUM_SUBFORUMS) {
				$items['create:subforum'] = array(
					'text' => elgg_echo('hj:forum:create:subforum'),
					'href' => "forum/create/forum/$entity->guid",
					'class' => 'elgg-button-create-entity',
					'parent_name' => 'options',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);
			}

			if ($entity->canWriteToContainer(0, 'object', 'hjforumtopic')) {
				$items['create:topic'] = array(
					'text' => elgg_echo('hj:forum:create:topic'),
					'href' => "forum/create/topic/$entity->guid",
					'class' => 'elgg-button-create-entity',
					'parent_name' => 'options',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 855
				);
			}

			if ($entity->canWriteToContainer(0, 'object', 'hjforumcategory') && HYPEFORUM_CATEGORIES && $entity->enable_subcategories) {
				$items['create:category'] = array(
					'text' => elgg_echo('hj:forum:create:category'),
					'href' => "forum/create/category/$entity->guid",
					'class' => 'elgg-button-create-entity',
					'parent_name' => 'options',
					'data-callback' => 'refresh:lists::framework',
					'data-toggle' => 'dialog',
					'priority' => 860
				);
			}

			break;

		case 'hjforumtopic' :
			if ($entity->canWriteToContainer(0, 'object', 'hjforumpost')) {
				$items['create:forumpost'] = array(
					'text' => elgg_echo('hj:forum:create:post'),
					'href' => "forum/create/post/$entity->guid#reply",
					'class' => 'elgg-button-create-entity',
					'parent_name' => 'options',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);
				$items['create:forumpost:quote'] = array(
					'text' => elgg_echo('hj:forum:create:post:quote'),
					'href' => "forum/create/post/$entity->guid?quote=$entity->guid#reply",
					'class' => 'elgg-button-create-entity',
					'parent_name' => 'options',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);
			}
			break;


		case 'hjforumpost' :
			$topic = $entity->getContainerEntity();
			if ($topic->canWriteToContainer(0, 'object', 'hjforumpost')) {
				$items['create:forumpost:quote'] = array(
					'text' => elgg_echo('hj:forum:create:post:quote'),
					'href' => "forum/create/post/$topic->guid?quote=$entity->guid#reply",
					'class' => 'elgg-button-create-entity',
					'parent_name' => 'options',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);
			}
			break;
			break;

		case 'hjforumcategory' :

			if (!$full) {
				$items = array(
					'edit' => array(
						'text' => elgg_echo('edit'),
						'href' => $entity->getEditURL(),
						'parent_name' => 'options',
						'class' => 'elgg-button-edit-entity',
						'data-toggle' => 'dialog',
						/** @todo: Add custom callback */
						//'data-callback' => 'refresh:lists::framework',
						'data-uid' => $entity->guid,
						'priority' => 850
					),
				);
			}

			break;
	}

	if ($items) {
		foreach ($items as $name => $item) {
			foreach ($return as $key => $val) {
				if (!$val instanceof ElggMenuItem) {
					unset($return[$key]);
				}
				if ($val instanceof ElggMenuItem && $val->getName() == $name) {
					unset($return[$key]);
				}
			}
			$item['name'] = $name;
			$return[$name] = ElggMenuItem::factory($item);
		}
	}

	return $return;
}

function hj_forum_entity_title_menu($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, false);

	if (!$entity instanceof hjObject)
		return $return;

	switch ($entity->getSubtype()) {

		case 'hjforum' :

			if ($entity->canWriteToContainer(0, 'object', 'hjforum') && HYPEFORUM_SUBFORUMS) {
				$items['create:subforum'] = array(
					'text' => elgg_echo('hj:forum:create:subforum'),
					'href' => "forum/create/forum/$entity->guid",
					'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);
			}

			if ($entity->canWriteToContainer(0, 'object', 'hjforumtopic')) {
				$items['create:topic'] = array(
					'text' => elgg_echo('hj:forum:create:topic'),
					'href' => "forum/create/topic/$entity->guid",
					'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 855
				);
			}

			if ($entity->canWriteToContainer(0, 'object', 'hjforumcategory') && HYPEFORUM_CATEGORIES && $entity->enable_subcategories) {
				$items['create:category'] = array(
					'text' => elgg_echo('hj:forum:create:category'),
					'href' => "forum/create/category/$entity->guid",
					'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'priority' => 860
				);
			}

			break;

		case 'hjforumtopic' :
			if ($entity->canWriteToContainer(0, 'object', 'hjforumpost')) {
				$items['create:forumpost'] = array(
					'text' => elgg_echo('hj:forum:create:post'),
					'href' => "forum/create/post/$entity->guid#reply",
					'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);
				$items['create:forumpost:quote'] = array(
					'text' => elgg_echo('hj:forum:create:post:quote'),
					'href' => "forum/create/post/$entity->guid?quote=$entity->guid#reply",
					'class' => 'elgg-button-create-entity',
					'parent_name' => 'options',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);
			}
			break;
	}

	if ($items) {
		foreach ($items as $name => $item) {
			foreach ($return as $key => $val) {
				if (!$val instanceof ElggMenuItem) {
					unset($return[$key]);
				}
				if ($val instanceof ElggMenuItem && $val->getName() == $name) {
					unset($return[$key]);
				}
			}
			$item['name'] = $name;
			$return[$name] = ElggMenuItem::factory($item);
		}
	}

	return $return;
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

				if (HYPEFORUM_CATEGORIES_TOP) {
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
			}

			break;

		case 'group' :

			$group = elgg_get_page_owner_entity();

			if ($group->canWriteToContainer()) {
				elgg_register_menu_item('title', array(
					'name' => 'create:forum',
					'text' => elgg_echo('hj:forum:create:forum'),
					'href' => "forum/create/forum/$group->guid",
					'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 100
				));

				if (HYPEFORUM_CATEGORIES_TOP) {
					elgg_register_menu_item('title', array(
						'name' => 'create:category',
						'text' => elgg_echo('hj:forum:create:category'),
						'href' => "forum/create/category/$group->guid",
						'class' => 'elgg-button elgg-button-action elgg-button-create-entity',
						'data-callback' => 'newcategory::framework:forum',
						'data-toggle' => 'dialog',
						'priority' => 200
					));
				}
			}
			break;
	}
}
