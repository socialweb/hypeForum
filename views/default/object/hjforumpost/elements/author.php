<?php
$user = $vars['entity'];

if (!$user) return true;

$name = elgg_view('output/url', array(
	'text' => $user->name,
	'href' => $user->getURL(),
	'is_trusted' => true
));

$icon = elgg_view_entity_icon($user, 'medium');

echo $name . '<br />' . $icon;