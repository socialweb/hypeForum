<?php

$entity = elgg_extract('entity', $vars);

if (HYPEFORUM_FORUM_TOPIC_ICON && isset($entity->icon)) {
	$icon = elgg_view('output/img', array(
		'src' => elgg_get_site_url() . 'mod/hypeForum/graphics/forumtopic/' . $entity->icon . '.png',
		'height' => 24,
		'width' => 24
			));
}

if (elgg_in_context('groups') && !elgg_instanceof(elgg_get_page_owner_entity(), 'group')) {
	$breadcrumbs = elgg_view('framework/bootstrap/object/elements/breadcrumbs', $vars);
}

$title = elgg_view('framework/bootstrap/object/elements/title', $vars);
$title = elgg_view_image_block($icon, $title);

$description = elgg_view('framework/bootstrap/object/elements/briefdescription', $vars);

echo elgg_view_module('forumtopic', $breadcrumbs . $title, $description);
