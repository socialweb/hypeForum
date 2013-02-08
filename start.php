<?php

/* hypeForum
 *
 * Forum functionality for Elgg
 * @package hypeJunction
 * @subpackage hypeForum
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyrigh (c) 2011-2013, Ismayil Khayredinov
 */

define('HYPEFORUM_RELEASE', 1360277917);

elgg_register_event_handler('init', 'system', 'hj_forum_init');

function hj_forum_init() {

	$plugin = 'hypeForum';

	// Make sure hypeFramework is active and precedes hypeForum in the plugin list
	if (!is_callable('hj_framework_path_shortcuts')) {
		register_error(elgg_echo('framework:error:plugin_order', array($plugin)));
		disable_plugin($plugin);
		forward('admin/plugins');
	}

	// Run upgrade scripts
	hj_framework_check_release($plugin, HYPEFORUM_RELEASE);

	$shortcuts = hj_framework_path_shortcuts($plugin);

	// Helper Classes
	elgg_register_classes($shortcuts['classes']);

	// Libraries
	elgg_register_library('hj:forum:base', $shortcuts['lib'] . 'forum/base.php');
	elgg_load_library('hj:forum:base');

	elgg_register_library('hj:forum:forms', $shortcuts['lib'] . 'forum/forms.php');
	elgg_load_library('hj:forum:forms');

	elgg_register_page_handler('forum', 'hj_forum_page_handler');

	elgg_register_menu_item('site', array(
		'name' => 'forum',
		'text' => elgg_echo('forums'),
		'href' => 'forum',
	));

	elgg_register_entity_type('object', 'hjforum');
	elgg_register_entity_type('object', 'hjforumtopic');
	elgg_register_entity_type('object', 'hjforumpost');

	// CSS and JS
	elgg_register_css('forum.base.css', elgg_get_simplecache_url('css', 'hj/forum/base'));
	elgg_register_simplecache_view('css/hj/forum/base');

	elgg_register_js('forum.base.js', elgg_get_simplecache_url('js', 'hj/forum/base'));
	elgg_register_simplecache_view('js/hj/forum/base');

	elgg_register_action('edit/object/hjforum', $shortcuts['actions'] . 'edit/object/hjforum.php');
	elgg_register_action('edit/object/hjforumtopic', $shortcuts['actions'] . 'edit/object/hjforumtopic.php');
	elgg_register_action('edit/object/hjforumpost', $shortcuts['actions'] . 'edit/object/hjforumpost.php');
	elgg_register_action('edit/object/hjforumcategory', $shortcuts['actions'] . 'edit/object/hjforumcategory.php');

	elgg_register_action('forum/order/categories', $shortcuts['actions'] . 'order/categories.php');

	elgg_register_plugin_hook_handler('order_by_clause', 'framework:lists', 'hj_forum_order_by_clauses');

	// Allow users to user forums as container entities
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'hj_forum_container_permissions_check');

	elgg_register_plugin_hook_handler('register', 'menu:hjentityhead', 'hj_forum_entity_menu');

//	register_notification_object('object', 'hjforumpost', elgg_echo('hj:forum:newpost'));
//	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'hj_forum_notify_message');
}

/**
 * Forum page handler
 */
function hj_forum_page_handler($page, $handler) {

	$plugin = 'hypeForum';
	$shortcuts = hj_framework_path_shortcuts($plugin);
	$pages = $shortcuts['pages'] . 'forum/';

	elgg_load_css('forum.base.css');
	elgg_load_js('forum.base.js');

	elgg_push_breadcrumb(elgg_echo('forums'), 'forum/dashboard/site');

	switch ($page[0]) {

		default :
		case 'dashboard' :

			$dashboard = elgg_extract(1, $page, 'site');
			set_input('dashboard', $dashboard);

			switch ($dashboard) {

				default :
				case 'site' :
					include "{$pages}dashboard/site.php";
					break;

				case 'groups' :
					include "{$pages}dashboard/groups.php";
					break;

				case 'group' :
					$group_guid = elgg_extract(2, $page, false);
					if (!$group_guid) {
						return false;
					}
					set_input('group_guid', $group_guid);
					include "{$pages}dashboard/group.php";
					break;
			}

			break;

		case 'create' :

			list($action, $subtype, $container_guid) = $page;

			if (!$subtype) {
				return false;
			}

			if (!$container_guid) {
				$site = elgg_get_site_entity();
				$container_guid = $site->guid;
			}

			elgg_set_page_owner_guid($container_guid);

			set_input('container_guid', $container_guid);

			$include = "{$pages}create/{$subtype}.php";

			if (!file_exists($include)) {
				return false;
			}

			include $include;
			break;

		case 'edit' :

			list($action, $guid) = $page;

			set_input('guid', $guid);

			$include = "{$pages}edit/object.php";

			if (!file_exists($include)) {
				return false;
			}

			include $include;
			break;

		case 'view' :
			if (!isset($page[1])) {
				return false;
			}
			$entity = get_entity($page[1]);

			if (!$entity)
				return false;

			echo elgg_view_page($entity->title, elgg_view_layout('entity', array('entity' => $entity)));
			break;
	}

	return true;
}

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
function hj_forum_filter_forum_list($list_id, $options) {

	$query = get_input("__q_$list_id", false);

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

	if (!$container || !$user || $subtype)
		return $return;

	switch ($container->getSubtype()) {

		default :
			return $return;
			break;

		case 'hjforum' :

			switch ($subtype) {

				default :
					return $return;

				case 'hjforum' : // Adding sub-forum
					return ($entity->canAdminister($user->guid) || $entity->canModerate($user->guid));
					break;

				case 'hjforumtopic' : // Adding new topics
					return ($entity->canAdminister($user->guid) || $entity->canModerate($user->guid) || $entity->isOpenFor($subtype));
					break;
			}
			break;

		case 'hjforumtopic' :
			switch ($subtype) {

				default :
					return $return;
					break;

				case 'hjforumtopic' :
					return ($entity->canAdminister($user->guid) || $entity->canModerate($user->guid) || $entity->isOpenFor($subtype));
					break;
			}
			break;
	}
}

/**
 * Forum menus
 */
function hj_forum_entity_menu($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, false);
	$full = elgg_extract('full_view', $params, false);

	if (!$entity instanceof hjObject)
		return $return;

	switch ($entity->getSubtype()) {

		case 'hjforum' :

			if ($entity->canWriteToContainer()) {
				$items['create:subforum'] = array(
					'text' => elgg_echo('hj:forum:create:subforum'),
					'href' => "forum/create/forum/$entity->guid",
					'class' => ($full) ? 'elgg-button elgg-button-action elgg-button-create-entity' : 'elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'data-callback' => ($full) ? 'refresh:lists::framework' : null,
					'priority' => 850
				);
			}

			if ($entity->canWriteToContainer()) {
				$items['create:topic'] = array(
					'text' => elgg_echo('hj:forum:create:topic'),
					'href' => "forum/create/topic/$entity->guid",
					'class' => ($full) ? 'elgg-button elgg-button-action elgg-button-create-entity' : 'elgg-button-create-entity',
					'data-toggle' => 'dialog',
					'data-callback' => ($full) ? 'refresh:lists::framework' : null,
					'priority' => 855
				);
			}

			if ($entity->canWriteToContainer()) {
				$items['create:category'] = array(
					'text' => elgg_echo('hj:forum:create:category'),
					'href' => "forum/create/category/$entity->guid",
					'class' => ($full) ? 'elgg-button elgg-button-action elgg-button-create-entity' : 'elgg-button-create-entity',
					'data-callback' => ($full) ? 'newcategory::framework:forum' : null,
					'data-toggle' => 'dialog',
					'priority' => 860
				);
			}

			break;

		case 'hjforumtopic' :
			if ($entity->canWriteToContainer()) {
				$items['create:forumpost'] = array(
					'text' => elgg_echo('hj:forum:create:post'),
					'href' => "forum/create/post/$entity->guid#reply",
					'class' => ($full) ? 'elgg-button elgg-button-action elgg-button-create-entity' : '',
					'data-toggle' => 'dialog',
					'data-callback' => 'refresh:lists::framework',
					'priority' => 850
				);

//				$items['create:forumpost:quote'] = array(
//					'text' => elgg_echo('hj:forum:create:post:quote'),
//					'href' => "forum/create/post/$entity->guid?quote=1#reply",
//					'class' => ($full) ? 'elgg-button elgg-button-action elgg-button-create-entity' : '',
//					'data-toggle' => 'dialog',
//					'data-callback' => 'refresh:lists::framework',
//					'priority' => 855
//				);
			}
			break;


		case 'hjforumpost' :
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

	if (!$full) {
		foreach ($return as $key => $item) {
			if ($item->getName() == 'options') {
				continue;
			}
			$item->setParentName('options');
			$return[$key] = $item;
		}
	}


	return $return;
}