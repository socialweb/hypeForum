<?php

$site = elgg_get_site_entity();

$options = array(
	'types' => 'object',
	'subtypes' => 'hjforumtopic',
	//'container_guids' => $site->guid,
	'limit' => 0
);

$list_options = array(
	'list_type' => 'table',
	'list_view_options' => array(
		'table' => array(
			'head' => array(
				'details' => array(
					'colspan' => array(
						'title' => array(
							'sortable' => true,
							'sort_key' => 'oe.title',
							'text' => elgg_echo('title')
						),
						'description' => false
					)
				),
				'threads_count' => array(
					'sortable' => true,
					'sort_key' => 'forum.threads_count'
				),
				'replies_count' => array(
					'sortable' => true,
					'sort_key' => 'forum.replies_count'
				),
				'last_post' => false,
				'options' => false
			)
		),
	)
);

$viewer_options = array(
	'full_view' => false
);

$content = hj_framework_view_list('siteforums', $options, $list_options, $viewer_options);

$title = elgg_echo('hj:forum:siteforums');

echo elgg_view_module('main', $title, $content);