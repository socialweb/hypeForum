<?php

$categories = elgg_get_entities(array(
	'types' => 'object',
	'subtypes' => 'hjforumcategory',
	'limit' => 0,
	'container_guid' => elgg_get_site_entity()->guid
		));

foreach ($categories as $category) {

	echo $category->title;
	$getter_options = array(
		'types' => 'object',
		'subtypes' => 'hjforum',
		'limit' => 0,
//		'relationship' => 'filed_in',
//		'relationship_guid' => $category->guid,
//		'inverse_relationship' => true,
	);

	$list_options = array(
		'list_type' => 'table',
		'list_view_options' => array(
			'table' => array(
				'head' => array(
					'forum' => array(
						'text' => elgg_echo('hj:forum:tablecol:forum'),
						'sortable' => true,
						'sort_key' => 'forum.title',
					),
					'topics' => array(
						'text' => elgg_echo('hj:forum:tablecol:topics'),
						'sortable' => true,
						'sort_key' => 'forum.topics'
					),
					'posts' => array(
						'text' => elgg_echo('hj:forum:tablecol:posts'),
						'sortable' => true,
						'sort_key' => 'forum.posts'
					),
					'last_post' => array(
						'text' => elgg_echo('hj:forum:tablecol:last_post'),
						'sortable' => true,
						'sort_key' => 'forum.posts'
					),
				)
			)
		)
	);

	$viewer_options = array(
		'full_view' => true
	);

	echo hj_framework_view_list("site-forums-$category->guid", $getter_options, $list_options, $viewer_options, 'elgg_get_entities_from_relationship');
}
