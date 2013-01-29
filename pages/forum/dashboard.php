<?php

$title = elgg_echo('forums');

elgg_register_title_button();

$content = elgg_list_entities(array(
	'types' => 'object',
	'subtypes' => 'hjforum',
	'limit' => 0,
	'container_guid' => elgg_get_site_entity()->guid,
));

$site = elgg_get_site_entity();
$content .= elgg_list_entities(array(
	'types' => 'object',
	'subtypes' => 'hjforum',
	'limit' => 0,
	'wheres' => array("e.container_guid != $site->guid"),
	//'group_by' => 'e.container_guid',
));

$content .= elgg_list_entities(array(
	'types' => 'object',
	'subtypes' => 'hjforumpost',
	'limit' => 0
));

//$content .= elgg_view('hj/forum/pages/world');

$layout = elgg_view_layout('content', array(
	'content' => $content,
	'title' => $title
));

echo elgg_view_page($title, $layout);
