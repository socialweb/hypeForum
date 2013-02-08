<?php

$site = elgg_get_site_entity();

$options = array(
	'types' => 'object',
	'subtypes' => 'hjforumcategory',
	'limit' => 0,
	'container_guid' => $site->guid
);

$options = hj_framework_get_order_by_clause('md.priority', 'ASC', $options);

$categories = elgg_get_entities($options);

echo elgg_view_entity_list($categories, array(
	'list_class' => 'forum-category-list'
));

//$vars['entity'] = $site;
//$vars['subtypes'] = array('hjforum');
//echo elgg_view('hj/forum/modules/uncategorized', $vars);