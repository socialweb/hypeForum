<?php

$entity = $vars['entity'];

if (!elgg_instanceof($entity, 'object', 'hjforum'))
	return true;

echo elgg_view('framework/bootstrap/entity/cover', $vars);

echo elgg_view('framework/bootstrap/entity/description', $vars);


$options = array(
	'types' => 'object',
	'subtypes' => 'hjforumcategory',
	'limit' => 0,
	'container_guid' => $entity->guid
);

$options = hj_framework_get_order_by_clause('md.priority', 'ASC', $options);


$categories = elgg_get_entities($options);

if ($categories) {
	echo elgg_view_entity_list($categories, array(
		'list_class' => 'forum-category-list'
	));
} else if ($entity->canWriteToContainer()) {
	echo '<div class="hj-framework-warning">' . elgg_echo('hj:forum:nocategories') . '</div>';
	echo elgg_view('forms/edit/object/hjforumcategory', array(
		'container_guid' => $entity->guid
	));
} else {
	register_error(elgg_echo('hj:forum:notsetup'));
	forward("forum");
}