<?php

$body = elgg_view('object/hjforumtopic/elements/forum', $vars);
$image_alt = elgg_view('object/hjforumtopic/elements/menu', $vars);

echo elgg_view_image_block('', $body, array(
	'image_alt' => $image_alt
));
