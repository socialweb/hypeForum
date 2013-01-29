<?php

$container_guid = get_input('container_guid');

$title = elgg_echo('hj:forum:create:category');

elgg_push_breadcrumb($title);

$content = elgg_view('forms/edit/object/hjforumcategory', array(
	'container_guid' => $container_guid
		));

$layout = elgg_view_layout('one_sidebar', array(
	'title' => $title,
	'content' => $content,
		));

echo elgg_view_page($title, $layout);
