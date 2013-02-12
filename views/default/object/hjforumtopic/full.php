<?php

$entity = $vars['entity'];

if (!elgg_instanceof($entity, 'object', 'hjforumtopic'))
	return true;

if (HYPEFORUM_FORUM_TOPIC_COVER) {
	$content .= elgg_view('framework/bootstrap/object/elements/cover', $vars);
}

$content .= elgg_view('object/hjforumtopic/elements/original_post', $vars);

$list_id = "ft$entity->guid";

$dbprefix = elgg_get_config('dbprefix');
$getter_options = array(
	'types' => 'object',
	'subtypes' => array('hjforumpost'),
	'container_guid' => $entity->guid,
	'order_by' => 'e.time_created ASC'
);

$offset = get_input("__off_$list_id", 0);
$limit = get_input("__lim_$list_id", 10);

$list_options = array(
	'list_type' => 'list',
	'list_class' => 'hj-forumtopic',
	'list_view_options' => array(),
	'pagination' => true,
);

$viewer_options = array(
	'full_view' => true
);

$goto_guid = get_input("__goto", null);

if ($goto_guid) {
	$offset = hj_framework_get_descendant_offset($goto_guid, $getter_options);
	if ($offset > $limit) {
		$offset = (ceil($offset / $limit) * $limit) - $limit;
	}
	set_input("__off_$list_id", $offset);
	set_input("__limi_$list_id", $limit);
}

if (!get_input("__ord_$list_id", false)) {
	set_input("__ord_$list_id", 'e.time_created');
	set_input("__dir_$list_id", 'ASC');
}

$content .= hj_framework_view_list($list_id, $getter_options, $list_options, $viewer_options, 'elgg_get_entities');

$footer .= '<div id="reply">' . elgg_view('forms/edit/object/hjforumpost', array(
	'container_guid' => $entity->guid
)) . '</div>';

$module = elgg_view_module('forum-topic', $title, $content, array(
	'footer' => $footer
));

echo $module;