<?php

$entity = elgg_extract('entity', $vars);
$full = elgg_extract('full_view', $vars, false);

if (!$entity) {
	return true;
}

$user = $entity->getOwnerEntity();

$friendly_time = date("F j, Y - ga T", $entity->time_created);

if (HYPEFORUM_STICKY && $entity->isSticky()) {
	$icon = elgg_view('output/img', array(
		'src' => elgg_get_site_url() . 'mod/hypeForum/graphics/forumtopic/sticky.png',
		'height' => $config['tiny']['h'],
		'width' => $config['tiny']['w'],
		'title' => elgg_echo('hj:forum:sticky')
			));
}

if ($full) {
	$author = elgg_view('object/hjforumpost/elements/author', array('entity' => $user));
	$author_signature = elgg_view('object/hjforumpost/elements/signature', array('entity' => $user));

	$description = elgg_view('framework/bootstrap/object/elements/description', $vars);
	$menu = elgg_view('framework/bootstrap/object/elements/menu', $vars);


	echo elgg_view_image_block($author, $icon . $friendly_time . $description . $author_signature, array(
		'image_alt' => $menu
	));
} else {
	echo $friendly_time . '<br />';
	echo elgg_view('output/url', array(
		'text' => elgg_echo('byline', array($user->name)),
		'href' => $entity->getURL()
	));
}