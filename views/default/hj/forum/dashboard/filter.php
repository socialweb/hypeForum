<?php

$filter_context = elgg_extract('filter_context', $vars, 'site');

$tabs = array(
	'site' => array(
		'text' => elgg_echo('hj:forum:dashboard:tabs:site'),
		'href' => 'forum/dashboard/site',
		'selected' => ($filter_context == 'site'),
		'priority' => 100,
	),
	'groups' => array(
		'text' => elgg_echo('hj:forum:dashboard:tabs:groups'),
		'href' => 'forum/dashboard/groups',
		'selected' => ($filter_context == 'groups'),
		'priority' => 200,
	)
);

foreach ($tabs as $name => $tab) {
	$tab['name'] = $name;

	elgg_register_menu_item('filter', $tab);
}

echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));
