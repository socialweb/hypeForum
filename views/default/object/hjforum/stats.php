<?php

$entity = elgg_extract('entity', $vars, false);

if (!$entity) return true;

$topics = $entity->countTopics();
$posts = $entity->countPosts();

echo $topics;