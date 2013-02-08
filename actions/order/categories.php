<?php

$priorities = get_input('priorities');

foreach ($priorities as $priority) {
	$category = get_entity($priority['guid']);
	if (elgg_instanceof($category) && $category->canEdit()) {
		$category->priority = (int)$priority['priority'];
	}
}

forward(REFERER);