<?php

elgg_register_plugin_hook_handler('init', 'form:edit:object:hjforum', 'hj_forum_init_forum_form');

function hj_forum_init_forum_form($hook, $type, $return, $params) {

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
				'value_type' => 'file'
			),
			'title' => array(
				'required' => true
			),
			'description' => array(
				'input_type' => 'plaintext',
			),
			'category' => 'hj_forum_get_forum_category_input_options',
			'access_id' => array(
				'input_type' => 'access'
			),
			'add_to_river' => array(
				'input_type' => 'hidden',
				'value' => true
			)
		)
	);

	return $config;
}

function hj_forum_forms_config($hook, $type, $return) {

	$return['edit:object:hjforum'] = array(
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
				'value_type' => 'file'
			),
			'title' => array(
				'required' => true
			),
			'description' => array(
				'input_type' => 'plaintext',
			),
			'category' => 'hj_forum_get_forum_category_input_options',
			'access_id' => array(
				'input_type' => 'access'
			)
		)
	);

	$return['edit:object:hjforumcategory'] = array(
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
				'required' => true
			),
			'description' => array(
				'input_type' => 'plaintext',
				'rows' => 3
			),
			'access_id' => array(
				'input_type' => 'hidden',
				'value' => ACCESS_PUBLIC
			)
		)
	);

	return $return;
}

function hj_forum_get_field_options($params) {

	$form_name = elgg_extract('form_name', $params, false);
	if (!$form_name)
		return array();

	$name = elgg_extract('name', $params, false);
	if (!$name)
		return array();

	switch ($form_name) {

		case 'edit:object:hjforum' :

			switch ($name) {

				default:

					break;
			}
			break;

		default :
			$return = array();
			break;
	}

	return $return;
}

function hj_forum_get_forum_category_input_options($params) {

	$container_guid = elgg_extract('container_guid', $params, 0);

	$dbprefix = elgg_get_config('dbprefix');

	$categories = elgg_get_entities(array(
		'types' => 'object',
		'subtypes' => 'hjforumcategory',
		'limit' => 0,
		'container_guids' => $container_guid,
		'joins' => array("JOIN {$dbprefix}objects_entity oe ON oe.guid = e.guid"),
		'order_by' => 'oe.title ASC'
			));

	if ($categories) {
		foreach ($categories as $category) {
			$options_values[$category->guid] = $category->title;
		}

		$options = array(
			'input_type' => 'dropdown',
			'options_values' => $options_values
		);
	} else {

		$options = array(
			'input_type' => 'text',
			'override_view' => 'output/url',
			'text' => elgg_echo('hj:forum:create:category'),
			'href' => "forum/create/category/$container_guid"
		);
	}

	return $options;
}