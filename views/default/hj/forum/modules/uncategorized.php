<?php

$container = elgg_extract('entity', $vars);
$subtypes = elgg_extract('subtypes', $vars, ELGG_ENTITIES_ANY_VALUE);

$title = false;

$dbprefix = elgg_get_config('dbprefix');
$getter_options = array(
	'types' => 'object',
	'subtypes' => $subtypes,
	'container_guid' => $container->guid,
	'wheres' => array("NOT EXISTS (
 			SELECT 1 FROM {$dbprefix}entity_relationships
 				WHERE guid_one = e.guid
 				AND relationship = 'filed_in'
 		)")
);

$list_options = array(
	'list_type' => 'table',
	'list_class' => 'hj-forumlist',
	'list_view_options' => array(
		'table' => array(
			'head' => array(
				'forum' => array(
					'text' => elgg_echo('hj:forum:tablecol:forum'),
					'sortable' => true,
					'sort_key' => 'oe.title',
				),
				'topics' => array(
					'text' => elgg_echo('hj:forum:tablecol:topics'),
					'sortable' => true,
					'sort_key' => 'forum.topics'
				),
				'posts' => array(
					'text' => elgg_echo('hj:forum:tablecol:posts'),
					'sortable' => true,
					'sort_key' => 'forum.posts'
				),
				'last_post' => array(
					'text' => elgg_echo('hj:forum:tablecol:last_post'),
					'sortable' => true,
					'sort_key' => 'e.last_action'
				),
				'menu' => array(
					'text' => '',
					'sortable' => false
				),
			)
		)
	),
	'list_pagination' => true
);

$viewer_options = array(
	'full_view' => true
);

if (!get_input('__ord_fdefault', false)) {
	set_input('__ord_fdefault', 'e.last_action');
	set_input('__dir_fdefault', 'DESC');
}

$content .= hj_framework_view_list("fdefault", $getter_options, $list_options, $viewer_options, 'elgg_get_entities');

echo elgg_view_module('forum-category', $title, $content);