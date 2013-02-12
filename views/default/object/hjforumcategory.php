<?php

$entity = $category = elgg_extract('entity', $vars);

$title = $entity->getTitle();

$container = $entity->getContainerEntity();

if ($container->canEdit()) {
	$handle = elgg_view_icon('cursor-drag-arrow', 'hj-draggable-element-handle');
}

$params = array(
	'entity' => $entity,
	'class' => 'elgg-menu-hjentityhead elgg-menu-hz elgg-menu-forum-category',
	'sort_by' => 'priority',
	'handler' => 'forumcategory',
	'dropdown' => false
);

$menu = elgg_view_menu('hjentityhead', $params);

$title = elgg_view_image_block($handle, $title, array(
	'image_alt' => $menu
));

$content = elgg_view('framework/bootstrap/object/elements/description', $vars);

$list_id = "fc$category->guid";

$subtype_ids = implode(',', array(
	get_subtype_id('object', 'hjforum'),
	get_subtype_id('object', 'hjforumtopic')
		));

$dbprefix = elgg_get_config('dbprefix');
$getter_options = array(
	'selects' => array("CAST(stickymsv.string AS SIGNED) AS stickyval"),
	'joins' => array(
		"JOIN {$dbprefix}metadata stickymd ON e.guid = stickymd.entity_guid",
		"JOIN {$dbprefix}metastrings stickymsn ON (stickymsn.string = 'sticky')",
		"LEFT JOIN {$dbprefix}metastrings stickymsv ON (stickymd.name_id = stickymsn.id AND stickymd.value_id = stickymsv.id)"
	),
	'types' => 'object',
	'subtypes' => array('hjforum', 'hjforumtopic'),
	'relationship' => 'filed_in',
	'relationship_guid' => $category->guid,
	'inverse_relationship' => true,
	'group_by' => 'e.guid',
	'order_by' => "FIELD(e.subtype, $subtype_ids), ISNULL(stickyval), stickyval DESC"
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
	'pagination' => true
);

$viewer_options = array(
	'full_view' => true
);

//if (!get_input("__ord_$list_id", false)) {
//	set_input("__ord_$list_id", 'e.last_action');
//	set_input("__dir_$list_id", 'DESC');
//}

$content .= hj_framework_view_list($list_id, $getter_options, $list_options, $viewer_options, 'elgg_get_entities_from_relationship');

$module = elgg_view_module('forum-category', $title, $content);

if ($entity->canEdit()) {
	$module = "<div id=\"uid-$entity->guid\"class=\"hj-draggable-element\" data-uid=\"$entity->guid\">$module</div>";
}

echo $module;