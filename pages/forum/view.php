<?php

$guid = get_input('guid');
$forum = get_entity($guid);

if (!$forum) return false;

hj_forum_push_breadcrumbs($guid);

$title = elgg_get_excerpt($forum->title, 50);

$content = elgg_view_entity($forum, array(
	'full_view' => true
));

$layout = elgg_view_layout('one_column', array(
	'title' => $title,
	'content' => $content
));

echo elgg_view_page($title, $layout);