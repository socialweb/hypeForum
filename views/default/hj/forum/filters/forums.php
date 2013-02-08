<?php

$list_id = $vars['list_id'];

$filter .= elgg_view('input/text', array(
	'name' => "__q_$list_id",
	'value' => get_input("__q_$list_id"),
	'placeholder' => elgg_echo('hj:framework:filter:keywords'),
	'class' => 'span10 pull-right'
));

$filter .= elgg_view('input/hidden', array(
	'name' => "__off_$list_id",
	'value' => 0
));

$filter .= elgg_view('input/submit', array(
	'value' => elgg_echo('filter'),
	'class' => 'pull-right'
));

echo '<div class="row-fluid">';

echo '<div class="span1">';
echo '<div class="hj-ajax-loader hj-loader-indicator hidden"></div>';
echo '</div>';

echo '<div class="hj-framework-list-filter offset5 span6 clearfix">';

echo elgg_view('input/form', array(
	'method' => 'GET',
	'action' => '',
	'disable_security' => true,
	'body' => $filter,
	'class' => 'pull-right'
));

echo '</div>';

echo '</div>';