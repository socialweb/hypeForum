<?php

$subtypes = array(
	'hjforumtopic' => 'hjForumTopic'
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}
