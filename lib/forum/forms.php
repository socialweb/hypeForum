<?php

elgg_register_plugin_hook_handler('init', 'form:edit:object:hjforum', 'hj_forum_init_forum_form');
elgg_register_plugin_hook_handler('init', 'form:edit:object:hjforumtopic', 'hj_forum_init_forumtopic_form');
elgg_register_plugin_hook_handler('init', 'form:edit:object:hjforumpost', 'hj_forum_init_forumpost_form');
elgg_register_plugin_hook_handler('init', 'form:edit:object:hjforumcategory', 'hj_forum_init_forumcategory_form');

function hj_forum_init_forum_form($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, null);
	$container_guid = ($entity) ? $entity->container_guid : elgg_extract('container_guid', $params, ELGG_ENTITIES_ANY_VALUE);
	$container = get_entity($container_guid);

	$config = array(
		'attributes' => array(
			'enctype' => 'multipart/form-data',
			'id' => 'form-edit-object-hjforum',
			'action' => 'action/edit/object/hjforum'
		),
		'fields' => array(
			'type' => array(
				'input_type' => 'hidden',
				'value' => 'object'
			),
			'subtype' => array(
				'input_type' => 'hidden',
				'value' => 'hjforum'
			),
			'icon' => array(
				'input_type' => 'entity_icon',
				'value_type' => 'file',
				'value' => (isset($entity->icontime))
			),
			'title' => array(
				'value' => $entity->title,
				'required' => true
			),
			'description' => array(
				'value' => $entity->description,
				'input_type' => 'longtext',
				'class' => 'elgg-input-longtext'
			),
			'category' => hj_forum_get_forum_category_input_options($entity, $container),
			'access_id' => array(
				'value' => $entity->access_id,
				'input_type' => 'access'
			),
			'add_to_river' => array(
				'input_type' => 'hidden',
				'value' => ($entity) ? false : true
			)
		)
	);

	return $config;
}

function hj_forum_init_forumtopic_form($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, null);
	$container_guid = ($entity) ? $entity->container_guid : elgg_extract('container_guid', $params, ELGG_ENTITIES_ANY_VALUE);
	$container = get_entity($container_guid);

	$config = array(
		'attributes' => array(
//			'enctype' => 'multipart/form-data',
			'id' => 'form-edit-object-hjforumtopic',
			'action' => 'action/edit/object/hjforumtopic'
		),
		'fields' => array(
			'type' => array(
				'input_type' => 'hidden',
				'value' => 'object'
			),
			'subtype' => array(
				'input_type' => 'hidden',
				'value' => 'hjforumtopic'
			),
			'icon' => array(
				'input_type' => 'radio',
				'options' => hj_forum_get_forum_icons($entity, $container),
				'class' => 'elgg-horizontal',
				'value' => ($entity) ? $entity->icon : 'default'
			),
			'title' => array(
				'value' => $entity->title,
				'required' => true
			),
			'description' => array(
				'value' => $entity->description,
				'input_type' => 'longtext',
				'class' => 'elgg-input-longtext',
				'ltrequired' => true // hack for tinymce longtext
			),
			'category' => hj_forum_get_forum_category_input_options($entity, $container),
			'access_id' => array(
				'value' => $entity->access_id,
				'input_type' => 'access'
			),
			'add_to_river' => array(
				'input_type' => 'hidden',
				'value' => ($entity) ? false : true
			)
		)
	);

	return $config;
}

function hj_forum_init_forumpost_form($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, null);
	$container_guid = ($entity) ? $entity->container_guid : elgg_extract('container_guid', $params, ELGG_ENTITIES_ANY_VALUE);
	$container = get_entity($container_guid);

	$config = array(
		'attributes' => array(
//			'enctype' => 'multipart/form-data',
			'id' => 'form-edit-object-hjforumpost',
			'action' => 'action/edit/object/hjforumpost'
		),
		'fields' => array(
			'type' => array(
				'input_type' => 'hidden',
				'value' => 'object'
			),
			'subtype' => array(
				'input_type' => 'hidden',
				'value' => 'hjforumpost'
			),
			'title' => array(
				'input_type' => 'hidden',
				'value' => "Re: $entity->title",
			),
			'quote' => array(
				'input_type' => 'hidden',
				'value' => get_input('quote', false)
			),
			'quote_body' => ($container) ? array(
				'input_type' => 'hidden',
				'override_view' => 'output/longtext',
				'value' => $congainer->description
			) : false,
			'description' => array(
				'value' => $entity->description,
				'input_type' => 'longtext',
				'class' => 'elgg-input-longtext',
				'ltrequired' => true
			),
			'access_id' => array(
				'value' => ($entity) ? $entity->access_id : $container->access_id,
				'input_type' => 'hidden'
			),
			'add_to_river' => array(
				'input_type' => 'hidden',
				'value' => ($entity) ? false : true
			)
		)
	);

	return $config;
}

function hj_forum_init_forumcategory_form($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, null);

	$config = array(
		'attributes' => array(
			'id' => 'form-edit-object-hjforumcategory',
			'action' => 'action/edit/object/hjforumcategory'
		),
		'fields' => array(
			'type' => array(
				'input_type' => 'hidden',
				'value' => 'object'
			),
			'subtype' => array(
				'input_type' => 'hidden',
				'value' => 'hjforumcategory'
			),
			'title' => array(
				'value' => $entity->title,
				'required' => true
			),
			'description' => array(
				'value' => $entity->description,
				'input_type' => 'longtext',
				'class' => 'elgg-input-logntext'
			),
			'access_id' => array(
				'value' => ($entity) ? $entity->access_id : ACCESS_PUBLIC,
				'input_type' => 'hidden'
			)
		)
	);

	return $config;
}

function hj_forum_get_forum_category_input_options($entity = null, $container = null) {

	if (!$entity && !$container)
		return false;

	$dbprefix = elgg_get_config('dbprefix');
	$categories = elgg_get_entities(array(
		'types' => 'object',
		'subtypes' => 'hjforumcategory',
		'limit' => 0,
		'container_guids' => $container->guid,
		'joins' => array("JOIN {$dbprefix}objects_entity oe ON oe.guid = e.guid"),
		'order_by' => 'oe.title ASC'
			));

	if ($categories) {
		foreach ($categories as $category) {
			$options_values[$category->guid] = $category->title;
		}

		if ($entity) {
			$categories = $entity->getCategories('hjforumcategory');
			$value = $categories[0]->guid;
		}
		
		$options = array(
			'input_type' => 'dropdown',
			'options_values' => $options_values,
			'value' => $value
		);
	} else {

		if ((elgg_instanceof($container, 'site') && elgg_is_admin_logged_in()) ||
				($container->canAdminister())) {
			$options = array(
				'input_type' => 'text',
				'override_view' => 'output/url',
				'text' => elgg_echo('hj:forum:create:category'),
				'href' => "forum/create/category/$container->guid"
			);
		} else {
			return false;
		}
	}

	return $options;
}

function hj_forum_get_forum_icons($entity = null, $container = null) {
	$options = array('default', 'star', 'heart', 'question', 'important', 'info', 'idea', 'laugh', 'surprise', 'lightning', 'announcement', 'lock');

	$options = elgg_trigger_plugin_hook('hj:forum:icons', 'all', null, $options);

	foreach ($options as $option) {
		$label = elgg_view_icon("forum-$option");
		$options_values["$label"] = $option;
	}

	return $options_values;
}