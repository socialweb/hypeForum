<?php

$entity = elgg_extract('entity', $vars, false);

if (!$entity) return true;

$metadata = elgg_view_menu('entity', array(
	'entity' => $entity,
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
	'handler' => 'hjforum'
));

if (elgg_in_context('widgets')) {
	$metadata = '';
}

$content = elgg_view('output/longtext', array(
	'value' => $entity->description
));

$content .= elgg_view('object/hjforum/stats', $vars);

$params = array(
	'entity' => $entity,
	'title' => '',
	'metadata' => $metadata,
	'subtitle' => false,
	'tags' => false,
	'content' => $content
);

echo elgg_view('object/elements/summary', $params);
