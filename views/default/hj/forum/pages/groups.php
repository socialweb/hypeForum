<?php

$user = elgg_get_page_owner_entity();
if (!$user) {
	$user = elgg_get_logged_in_user_entity();
}
if (!$user) {
	return true;
}

$groups = get_users_membership($user->guid);

if (!$groups) {
	return true;
}

$group_guids = array(0);
foreach ($groups as $group) {
	$group_guids[] = $group->guid;
}

$forums = elgg_get_entities(array(
	'types' => 'object',
	'subtypes' => 'hjforumtopic',
	'container_guids' => $group_guids,
	'limit' => 0
		));

$list = array();

foreach ($forums as $forum) {
	if (!isset($forum->instance)) {
		$instance = 'mainforum';
	} else {
		$instance = $forum->instance;
	}
	$list[$forum->container_guid][$instance][] = $forum;
}

foreach ($list as $group_guid => $group_list) {
	$group = get_entity($group_guid);

	$content = '';

	if (count($group_list) > 1) {
		foreach ($group_list as $key => $value) {
			$title = elgg_echo("hj:forum:instance:$key");
			$body = elgg_view_entity_list($group_list[$key], array(
				'full_view' => false
					));
			$content .= elgg_view_module('forumgrouping', $title, $body);
		}
	} else {
		$content .= elgg_view_entity_list(end($group_list[$group_guid]), array(
			'full_view' => false
				));
	}

	$mod_content .= elgg_view_module('forumgrouping', $group->name, $content);
}
$title = elgg_echo('hj:forum:groupforums');

echo elgg_view_module('main', $title, $mod_content);