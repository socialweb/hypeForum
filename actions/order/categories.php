<?php

$priorities = get_input('uid');

for($i=0; $i<count($priorities);$i++) {
	$category = get_entity($priorities[$i]);
	if (elgg_instanceof($category) && $category->canEdit()) {
		$category->priority = $i*10+1;
	}
}

forward(REFERER);