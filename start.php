<?php

/* hypeForum
 *
 * Forum functionality for Elgg
 * @package hypeJunction
 * @subpackage hypeForum
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyrigh (c) 2011, Ismayil Khayredinov
 */

define('HYPEFORUM_RELEASE', 1359476073);

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

	// Register URL and Page handlers
	elgg_register_entity_url_handler('object', 'hjforum', 'hj_forum_forum_url_handler');
	elgg_register_entity_url_handler('object', 'hjforumtopic', 'hj_forum_forumtopic_url_handler');
	elgg_register_entity_url_handler('object', 'hjforumpost', 'hj_forum_forumpost_url_handler');

	elgg_register_page_handler('forum', 'hj_forum_page_handler');

	elgg_register_menu_item('site', array(
		'name' => 'forum',
		'text' => elgg_echo('forums'),
		'href' => 'forum',
	));

	// CSS and JS
	elgg_register_css('hj.forum.base', elgg_get_simplecache_url('css', 'hj/forum/base'));
	elgg_register_simplecache_view('css/hj/forum/base');

	elgg_register_js('hj.forum.base ', elgg_get_simplecache_url('js', 'hj/forum/base'));
	elgg_register_simplecache_view('js/hj/forum/base');

	elgg_register_action('edit/object/hjforum', $shortcuts['actions'] . 'edit/object/hjforum.php');
	elgg_register_action('edit/object/hjforumtopic', $shortcuts['actions'] . 'edit/object/hjforumtopic.php');
	elgg_register_action('edit/object/hjforumpost', $shortcuts['actions'] . 'edit/object/hjforumpost.php');
	elgg_register_action('edit/object/hjforumcategory', $shortcuts['actions'] . 'edit/object/hjforumcategory.php');

	elgg_register_plugin_hook_handler('order_by_clause', 'framework:lists', 'hj_forum_order_by_clauses');

	// ==================	review =====================//
	// Allow writing to hjforum containers
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'hj_forum_container_permissions_check');

	elgg_register_plugin_hook_handler('register', 'menu:hjentityhead', 'hj_forum_entity_menu');
	elgg_register_plugin_hook_handler('register', 'menu:hjsegmenthead', 'hj_forum_main_menu');
	elgg_register_plugin_hook_handler('register', 'menu:hjsectionfoot', 'hj_forum_topic_menu');

	elgg_register_event_handler('create', 'object', 'hj_forum_topic_segment_setup');
	elgg_register_event_handler('update', 'object', 'hj_forum_topic_segment_setup');

	elgg_register_entity_type('object', 'hjforumtopic');
	elgg_register_plugin_hook_handler('search_types', 'get_types', 'hj_forum_custom_types_posts_hook');
	elgg_register_plugin_hook_handler('search', 'hjforumpost', 'hj_forum_search_posts_hook');

	elgg_register_plugin_hook_handler('hj:framework:field:process', 'all', 'hj_forum_add_last_action');

	register_notification_object('object', 'hjforumpost', elgg_echo('hj:forum:newpost'));
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'hj_forum_notify_message');
}

function hj_forum_forum_url_handler($entity) {
	
}

function hj_forumtopic_url_handler($entity) {
	$friendly_title = elgg_get_friendly_title($entity->title);
	return "forum/view/{$entity->guid}/{$friendly_title}";
}

function hj_forum_page_handler($page, $handler) {

	$plugin = 'hypeForum';
	$shortcuts = hj_framework_path_shortcuts($plugin);
	$pages = $shortcuts['pages'] . 'forum/';

	elgg_load_css('hj.forum.base');
	elgg_load_js('hj.forum.base');

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


		case 'dashboard' :
			// user's forum dash - latest, bookmarked etc
			break;

		case 'view' :
			if (!isset($page[1])) {
				return false;
			}
			set_input('guid', $page[1]);
			include "{$pages}view.php";
			break;

		case 'edit' :

			break;

		case 'add' :
			if (!isset($page[1])) {
				$page[1] = elgg_get_site_entity()->guid;
			}
			set_input('container_guid', $page[1]);
			if (!isset($page[2])) {
				$page[2] = 'hjforumtopic';
			}
			set_input('object', $page[2]);
			include "{$pages}add.php";
			break;
	}

	return true;

	if ($page[0] == 'sync') {
		include "{$pages}sync.php";
		return true;
	}

	$entity_guid = elgg_extract(0, $page, false);
	$entity = get_entity($entity_guid);

	if (!$entity) {
		include "{$pages}forum.php";
		return true;
	}

	if ($entity->children && $entity->children == 'forumtopic') {
		$segment_container_guid = $entity->guid;
	} else {
		$segment_container_guid = $entity->container_guid;
	}
	$segments = hj_framework_get_entities_by_priority('object', 'hjsegment', null, $segment_container_guid);

	if (is_array($segments)) {
		$bread = true;
		$crumb = $segments[0];
		$crumbs[] = $crumb;
		while ($bread) {
			if (elgg_instanceof($crumb, 'object', 'hjforumtopic')) {
				$crumbs[] = $crumb;
			}
			$crumb = get_entity($crumb->container_guid);
			if (!elgg_instanceof($crumb, 'object', 'hjsegment') && !elgg_instanceof($crumb, 'object', 'hjforumtopic')) {
				$bread = false;
			}
		}
		for ($i = sizeof($crumbs) - 1; $i > 0; $i--) {
			elgg_push_breadcrumb($crumbs[$i]->title, $crumbs[$i]->getURL());
		}
	}

	set_input('e', $entity_guid);

	if ($entity->children && $entity->children == 'forumtopic') {
		include "{$pages}forum.php";
		return true;
	} else {
		include "{$pages}topic.php";
		return true;
	}
}

function hj_forum_container_permissions_check($hook, $type, $return, $params) {
	$container = elgg_extract('container', $params, false);
	$subtype = elgg_extract('subtype', $params, false);

	if (elgg_instanceof($container, 'object', 'hjforumtopic')
			|| $subtype == 'hjforumtopic') {
		return true;
	}

	return $return;
}

function hj_forum_get_forum_section_types_hook($hook, $type, $return, $params) {
	$context = elgg_extract('context', $params, false);

	if ($context && $context == 'forum') {
		$return = array('hjforumtopic');
	}
	return $return;
}

function hj_forum_entity_menu($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params);

	if (elgg_instanceof($entity, 'object', 'hjforumtopic') && $entity->canAnnotate()) {
		$data = hj_framework_json_query($params['reply_params']);
		unset($params['reply_params']);

		$reply = array(
			'name' => 'reply',
			'title' => elgg_echo('hj:forum:reply'),
			'text' => elgg_echo('hj:forum:reply'),
			'rel' => 'fancybox',
			'href' => "action/framework/entities/edit",
			'data-options' => $data,
			'class' => "hj-ajaxed-edit",
			'priority' => 300,
			'section' => 'dropdown'
		);
		$return[] = ElggMenuItem::factory($reply);
	}

	if (elgg_instanceof($entity, 'object', 'hjforumtopic') && $entity->canAdminister()) {
		$params = hj_framework_extract_params_from_params($params['params']);
		$data = hj_framework_json_query($params);

		$edit = array(
			'name' => 'edit',
			'title' => elgg_echo('hj:framework:edit'),
			'text' => elgg_echo('hj:framework:edit'),
			'rel' => 'fancybox',
			'href' => "action/framework/entities/edit",
			'data-options' => $data,
			'class' => "hj-ajaxed-edit",
			'priority' => 800,
			'section' => 'dropdown'
		);
		$return[] = ElggMenuItem::factory($edit);

// AJAXed Delete Button
		$delete = array(
			'name' => 'delete',
			'title' => elgg_echo('hj:framework:delete'),
			'text' => elgg_echo('hj:framework:delete'),
			'href' => "action/framework/entities/delete?e=$entity->guid",
			'class' => 'hj-ajaxed-remove',
			'id' => "hj-ajaxed-remove-{$entity->guid}",
			'priority' => 900,
			'section' => 'dropdown'
		);
		$return[] = ElggMenuItem::factory($delete);
	}

	return $return;
}

function hj_forum_main_menu($hook, $type, $return, $params) {

// Extract available parameters
	$entity = elgg_extract('entity', $params);

	$container_guid = elgg_extract('container_guid', $params['params']);
	$container = get_entity($container_guid);

	$section = elgg_extract('subtype', $params['params']);
	$handler = elgg_extract('handler', $params['params']);

	$data = hj_framework_json_query($params);
	$url = hj_framework_http_build_query($params);

	if (elgg_instanceof($entity, 'object', 'hjsegment') && $entity->handler == 'hjforumtopic') {
		$return = array();
	}

	if (elgg_instanceof($container, 'object', 'hjforumtopic') && $container->canAdminister()) {
// Add widget
		$widget = array(
			'name' => 'widget',
			'title' => elgg_echo('hj:forum:addnewcategory'),
			'text' => elgg_view('input/button', array('value' => elgg_echo('hj:forum:addnewcategory'), 'class' => 'elgg-button-action')),
			'href' => "action/framework/widget/add",
			'data-options' => $data,
			'id' => "hj-ajaxed-addwidget-{$entity->guid}",
			'class' => "hj-ajaxed-addwidget",
			'target' => "elgg-object-{$entity->guid}",
			'priority' => 100
		);
		$return[] = ElggMenuItem::factory($widget);

		$cont_params = hj_framework_extract_params_from_entity($container);
		$cont_params['target'] = "elgg-object-$container->guid";
		$data = hj_framework_json_query($cont_params);


		$edit = array(
			'name' => 'edit',
			'title' => elgg_echo('hj:framework:edit'),
			'text' => elgg_view('input/button', array('value' => elgg_echo('hj:framework:edit'), 'class' => 'elgg-button-action')),
			'rel' => 'fancybox',
			'href' => "action/framework/entities/edit",
			'is_action' => true,
			'data-options' => $data,
			'class' => "hj-ajaxed-edit",
			'priority' => 800
		);
		$return[] = ElggMenuItem::factory($edit);

// AJAXed Delete Button
		$delete = array(
			'name' => 'delete',
			'title' => elgg_echo('hj:framework:delete'),
			'text' => elgg_view('input/button', array('value' => elgg_echo('hj:framework:delete'), 'class' => 'elgg-button-action')),
			'href' => "action/framework/entities/delete?e=$container->guid",
			'is_action' => true,
			'class' => 'hj-ajaxed-remove',
			'id' => "hj-ajaxed-remove-{$entity->guid}",
			'priority' => 900,
		);
		$return[] = ElggMenuItem::factory($delete);
	}

	return $return;
}

function hj_forum_topic_menu($hook, $type, $return, $params) {

	$container_guid = elgg_extract('container_guid', $params['params']);
	$container = get_entity($container_guid);

	$widget_guid = elgg_extract('widget_guid', $params['params']);
	$widget = get_entity($widget_guid);

	$segment_guid = elgg_extract('segment_guid', $params['params']);
	$segment = get_entity($segment_guid);

	$section = elgg_extract('subtype', $params['params']);

	$data = hj_framework_json_query($params);

	if ($container && elgg_instanceof($container) && $section == 'hjforumtopic' && elgg_is_logged_in()) {
		$return = array();
		if ($container->canAdminister() || ($container->status == 'open' && $container->getContainerEntity()->getSubtype() == 'hjforumtopic')) {
// AJAXed Add Button
			$add = array(
				'name' => 'add',
				'title' => elgg_echo('hj:forum:addnewforumtopic'),
				'text' => elgg_view('input/button', array('value' => elgg_echo('hj:forum:addnewforumtopic'), 'class' => 'elgg-button-action')),
				'href' => "action/framework/entities/edit",
				'data-options' => $data,
				'is_action' => true,
				'rel' => 'fancybox',
				'class' => "hj-ajaxed-add",
				'priority' => 200);
			$return[] = ElggMenuItem::factory($add);
		}
	}

	return $return;
}

function hj_forum_topic_segment_setup($event, $object_type, $entity) {

	if ($entity->getSubtype() == 'hjforumtopic') {
		$forum = $entity;

		$segments = elgg_get_entities(array(
			'type' => 'object',
			'subtype' => 'hjsegment',
			'container_guid' => $forum->guid,
			'limit' => 1
				));

		$segment = new ElggObject($segments[0]->guid);
		$segment->title = $forum->title;
		$segment->access_id = $forum->access_id;
		$segment->owner_guid = $forum->owner_guid;
		$segment->subtype = 'hjsegment';
		$segment->container_guid = $forum->guid;
		$segment_guid = $segment->save();

		$segment->priority = 1;
		$segment->handler = 'hjforumtopic';

		if ($segment_guid) {
			$segment = get_entity($segment_guid);
			$segment->addWidget('hjforumtopic', null, 'forum');
		}
	}

	return true;
}

function hj_forum_custom_types_posts_hook($hook, $type, $return, $params) {
	$return[] = 'hjforumpost';
	return $return;
}

function hj_forum_search_posts_hook($hook, $type, $value, $params) {
	$db_prefix = elgg_get_config('dbprefix');

	$query = sanitise_string($params['query']);
	$limit = sanitise_int($params['limit']);
	$offset = sanitise_int($params['offset']);

	$params['type_subtype_pairs'] = array('object' => 'hjannotation');
	$params['metadata_name_value_pairs'] = array(
		'name' => 'annotation_name', 'value' => 'hjforumpost', 'operand' => '='
	);

	$params['joins'] = array(
		"JOIN {$db_prefix}metadata md on e.guid = md.entity_guid",
		"JOIN {$db_prefix}metastrings msn_n on md.name_id = msn_n.id",
		"JOIN {$db_prefix}metastrings msv_n on md.value_id = msv_n.id"
	);

	$fields = array('string');
	$params['wheres'] = array(
		"(msn_n.string = 'annotation_value')",
		search_get_where_sql('msv_n', $fields, $params, FALSE)
	);

	$params['count'] = TRUE;
	$count = elgg_get_entities_from_metadata($params);

	// no need to continue if nothing here.
	if (!$count) {
		return array('entities' => array(), 'count' => $count);
	}

	$params['count'] = FALSE;
	$entities = elgg_get_entities_from_metadata($params);

	// add the volatile data for why these entities have been returned.
	foreach ($entities as $key => $entity) {
		$desc = search_get_highlighted_relevant_substrings($entity->annotation_value, $params['query']);
		$entity->setVolatileData('search_annotation_value', $desc);
	}

	return array(
		'entities' => $entities,
		'count' => $count,
	);
}

function hj_forum_add_last_action($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params, false);

	if (elgg_instanceof($entity, 'object', 'hjannotation') && $entity->annotation_name == 'hjforumpost') {
		$forum = get_entity($entity->container_guid);
		$check = true;
		$parent = $forum;
		while ($check) {
			update_entity_last_action($parent->guid, time());
			$parent = $parent->getContainerEntity();
			if (elgg_instanceof($parent, 'object', 'hjforumtopic')) {
				$return = $parent;
			} else {
				$check = false;
			}
		}
	}
	return $return;
}

function hj_forum_order_by_clauses($hook, $type, $options, $params) {

	$order_by = $params['order_by'];
	$direction = $params['direction'];

	$prefix = 'e';
	$column = 'time_created';

	list($prefix, $column) = explode('.', $order_by);

	$prefix = sanitize_string($prefix);
	$column = sanitize_string($column);
	$direction = sanitize_string($direction);

	if (!$prefix || !$column) {
		return $options;
	}

	if ($prefix !== 'forum') {
		return $options;
	}

	$dbprefix = elgg_get_config('dbprefix');
	switch ($column) {

		case 'title' :
			$options['joins'][] = "JOIN {$dbprefix}objects_entity forum ON forum.guid = e.guid";
			$options['order_by'] = "forum.title $direction";
			break;

		case 'topics' :
			$ftsid = get_subtype_id('object', 'hjforumtopic');
			//$fsid = get_subtype_id('object', 'hjforum');

			$options['selects'][] = "count(topic.guid) as topiccount";

			$options['joins'][] = "LEFT JOIN {$dbprefix}entities topic ON (topic.subtype IN ($ftsid))";

			$options['joins'][] = "JOIN {$dbprefix}metadata topicbcmd ON topicbcmd.entity_guid = topic.guid";
			$options['joins'][] = "JOIN {$dbprefix}metastrings topicbcmsn ON (topicbcmd.name_id = topicbcmsn.id)";
			$options['joins'][] = "JOIN {$dbprefix}metastrings topicbcmsv ON (topicbcmd.value_id = topicbcmsv.id)";

			$options['wheres'][] = "(topicbcmsn.string = 'breadcrumbs' AND topicbcmsv.string LIKE CONCAT('%\"', e.guid, '\"%'))";
			$options['wheres'][] = get_access_sql_suffix('topic');

			$options['group_by'] = 'e.guid';
			$options['order_by'] = "count(topic.guid) $direction, e.time_created DESC";
			
			break;

		case 'posts' :
			$fpsid = get_subtype_id('object', 'hjforumpost');

			$options['selects'][] = "count(post.guid) as postscount";

			$options['joins'][] = "LEFT JOIN {$dbprefix}entities post ON (post.subtype = $fpsid)";

			$options['joins'][] = "JOIN {$dbprefix}metadata postbcmd ON postbcmd.entity_guid = post.guid";
			$options['joins'][] = "JOIN {$dbprefix}metastrings postbcmsn ON (postbcmd.name_id = postbcmsn.id)";
			$options['joins'][] = "JOIN {$dbprefix}metastrings postbcmsv ON (postbcmd.value_id = postbcmsv.id)";

			$options['wheres'][] = "(postbcmsn.string = 'breadcrumbs' AND postbcmsv.string LIKE CONCAT('%\"', e.guid, '\"%'))";
			$options['wheres'][] = get_access_sql_suffix('post');

			$options['group_by'] = 'e.guid';
			$options['order_by'] = "count(post.guid) $direction, e.time_created DESC";
			break;

		case 'last_action' :
			$options['order_by'] = "e.last_action $direction";
			break;

		default :
		case 'date' :
			$options['order_by'] = "e.time_created $direction";
			break;

		case 'author' :
			$options['joins'][] = "JOIN {$dbprefix}users_entity ue ON ue.guid = e.owner_guid";
			$options['order_by'] = "ue.name $direction";
			break;
	}

	print_r($options);
	
	return $options;
}