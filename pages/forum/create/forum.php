<?php

$container_guid = get_input('container_guid');
$container = get_entity($container_guid);

if (elgg_instanceof($container, 'site')) {
	elgg_push_breadcrumb(elgg_echo('hj:forum:dashboard:site'), 'forum/dashboard/site');
} else if (elgg_instanceof($container, 'group')) {
	elgg_push_breadcrumb(elgg_echo('hj:forum:dashboard:site'), "forum/dashboard/group/$container->guid");
} else if (elgg_instanceof($container, 'hjforumtopic')) {
	
}

$title = elgg_echo('hj:forum:create:forum');

elgg_push_breadcrumb($title);

$content = elgg_view('forms/edit/object/hjforum', array(
	'container_guid' => $container_guid
));

$layout = elgg_view_layout('one_sidebar', array(
	'title' => $title,
	'content' => $content,
));

echo elgg_view_page($title, $layout);
