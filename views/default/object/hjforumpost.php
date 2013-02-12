<?php

$entity = elgg_extract('entity', $vars);
$full = elgg_extract('full_view', $vars, false);

if (!$entity) {
	return true;
}

$user = $entity->getOwnerEntity();

$friendly_time = date("F j, Y - ga T", $entity->time_created);

if ($full) {
	$author = elgg_view('object/hjforumpost/elements/author', array('user' => $user));


	$description = elgg_view('framework/bootstrap/object/elements/description', $vars);
	$menu = elgg_view('framework/bootstrap/object/elements/menu', $vars);


	echo elgg_view_image_block($author, $friendly_time . $description, array(
		'image_alt' => $menu
	));
} else {
	echo $friendly_time . '<br />';
	echo elgg_view('output/url', array(
		'text' => elgg_echo('byline', array($user->name)),
		'href' => $entity->getURL()
	));
}