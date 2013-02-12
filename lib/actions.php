<?php

$shortcuts = hj_framework_path_shortcuts('hypeForum');
// Actions
elgg_register_action('edit/object/hjforum', $shortcuts['actions'] . 'edit/object/hjforum.php');
elgg_register_action('edit/object/hjforumtopic', $shortcuts['actions'] . 'edit/object/hjforumtopic.php');
elgg_register_action('edit/object/hjforumpost', $shortcuts['actions'] . 'edit/object/hjforumpost.php');
elgg_register_action('edit/object/hjforumcategory', $shortcuts['actions'] . 'edit/object/hjforumcategory.php');

elgg_register_action('forum/order/categories', $shortcuts['actions'] . 'order/categories.php');
