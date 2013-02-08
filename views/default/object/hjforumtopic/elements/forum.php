<?php

$entity = $vars['entity'];

$icon = elgg_view_icon("forum-$entity->icon");

$title = elgg_view('framework/bootstrap/entity/title', $vars);
$description = elgg_view('framework/bootstrap/entity/briefdescription', $vars);

echo elgg_view_image_block($icon, $title . $description);
