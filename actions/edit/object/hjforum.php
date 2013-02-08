<?php

$result = hj_framework_edit_object_action();

if ($result) {
	print json_encode(array('guid' => $result['entity']->guid));
	if (!$result['entity']->hasCategories('hjforumcategory')) {
		forward("forum/create/category/{$result['entity']->guid}");
	} else {
		forward($result['forward']);
	}
} else {
	forward(REFERER);
}
