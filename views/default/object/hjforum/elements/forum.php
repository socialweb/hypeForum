<?php

$vars['size'] = 'medium';

$icon = elgg_view('framework/bootstrap/entity/icon', $vars);
$title = elgg_view('framework/bootstrap/entity/title', $vars);
$description = elgg_view('framework/bootstrap/entity/description', $vars);

echo elgg_view_image_block($icon, $title . $description);
