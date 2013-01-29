<?php

$entity = elgg_extract('entity', $vars, false);

if (!$entity) {
	return false;
}

$categories = elgg_get_entities(array(
	'types' => 'object',
	'subtypes' => 'hjcategory',
	'container_guids' => $entity->guid,
	'limit' => 0
		));

foreach ($categories as $category) {

	echo elgg_list_entities_from_relationship(array(
		'types' => 'object',
		'subtypes' => 'hjforumtopic',
		'relationship' => 'category',
		'relationship_guid' => $category->guid,
		'inverse_relationship' => false,
		'limit' => 0,
		'full_view' => false
	));
}
