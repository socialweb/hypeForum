<?php

// hypeForum upgrade scripts

run_function_once('hj_forum_1358206168');
run_function_once('hj_forum_1358285155');
run_function_once('hj_forum_1359738428');
run_function_once('hj_forum_1360277917');

function hj_forum_1358206168() {

	ini_set('memory_limit', '512M');
	ini_set('max_execution_time', '500');

	$ia = elgg_set_ignore_access(true);

	$subtypes = array(
		'hjforum' => 'hjForum',
		'hjforumtopic' => 'hjForumTopic',
	);

	foreach ($subtypes as $subtype => $class) {
		if (get_subtype_id('object', $subtype)) {
			update_subtype('object', $subtype, $class);
		} else {
			add_subtype('object', $subtype, $class);
		}
	}

	$subtypeIdForum = get_subtype_id('object', 'hjforum');
	$subtypeIdForumTopic = get_subtype_id('object', 'hjforumtopic');
	$subtypeIdAnnotation = get_subtype_id('object', 'hjannotation');

	$dbprefix = elgg_get_config('dbprefix');

	$segments = elgg_get_entities_from_metadata(array(
		'types' => 'object',
		'subtypes' => 'hjsegment',
		'metadata_name_value_pairs' => array(
			'name' => 'handler',
			'value' => 'hjforumtopic'
		)
			));

	/**
	 * Upgrade :
	 * 1. Convert segmented hjForumTopic objects to hjForum objects
	 * 2. Remove segments
	 * 3. Convert widgets to categories
	 */
	foreach ($segments as $segment) {

		$forum = get_entity($segment->container_guid);

		$query = "UPDATE {$dbprefix}entities SET subtype = $subtypeIdForum WHERE subtype = $subtypeIdForumTopic AND guid = $forum->guid";
		update_data($query);

		$widgets = elgg_get_entities(array(
			'types' => 'object',
			'subtypes' => 'widget',
			'container_guids' => $segment->guid,
			'limit' => 0
				));

		foreach ($widgets as $widget) {

			$threads = elgg_get_entities_from_metadata(array(
				'types' => 'object',
				'subtypes' => 'hjforumtopic',
				'metadata_name_value_pairs' => array(
					array('name' => 'widget', 'value' => $widget->guid)
				),
				'limit' => 0,
					));

			$cat = new ElggObject();
			$cat->subtype = 'hjforumcategory';
			$cat->owner_guid = elgg_get_logged_in_user_guid();
			$cat->container_guid = $forum->guid;
			$cat->title = $widget->title;
			$cat->description = '';
			$cat->access_id = ACCESS_PUBLIC;
			$cat->save();

			foreach ($threads as $thread) {
				$thread->container_guid = $forum->guid; // make sure the thread is right under forum entity
				unset($thread->widget);
				$thread->save();

				add_entity_relationship($thread->guid, 'filed_in', $cat->guid);
				add_entity_relationship($forum->guid, 'forum', $thread->guid);


				$dbprefix = elgg_get_config('dbprefix');

				$query = "	SELECT guid FROM {$dbprefix}entities e
					JOIN {$dbprefix}metadata md ON md.entity_guid = e.guid
					JOIN {$dbprefix}metastrings msn ON msn.id = md.name_id
					JOIN {$dbprefix}metastrings msv ON msv.id = md.value_id
					WHERE subtype = $subtypeIdAnnotation AND msn.string = 'handler' AND msv.string = 'hjforumpost' AND e.container_guid = $thread->guid ";

				$guids = get_data($query);

				if (count($guids)) {
					foreach ($guids as $post) {
						add_entity_relationship($forum->guid, 'forum', $post->guid);
					}
				}
			}

			$widget->delete();
		}

		$segment->delete();
	}

	elgg_set_ignore_access($ia);
}

function hj_forum_1358285155() {

	$ia = elgg_set_ignore_access(true);

	$subtypes = array(
		'hjforumpost' => 'hjForumPost'
	);

	foreach ($subtypes as $subtype => $class) {
		if (get_subtype_id('object', $subtype)) {
			update_subtype('object', $subtype, $class);
		} else {
			add_subtype('object', $subtype, $class);
		}
	}

	$subtypeIdForumPost = get_subtype_id('object', 'hjforumpost');
	$subtypeIdAnnotation = get_subtype_id('object', 'hjannotation');

	$dbprefix = elgg_get_config('dbprefix');

	/**
	 * Upgrade :
	 * 1. Convert hjAnnotations objects for hjforumpost handlers to hjForumPost object
	 */
	$query = "	UPDATE {$dbprefix}entities e
				JOIN {$dbprefix}metadata md ON md.entity_guid = e.guid
				JOIN {$dbprefix}metastrings msn ON msn.id = md.name_id
				JOIN {$dbprefix}metastrings msv ON msv.id = md.value_id
				SET e.subtype = $subtypeIdForumPost
				WHERE subtype = $subtypeIdAnnotation AND msn.string = 'handler' AND msv.string = 'hjforumpost'	";

	update_data($query);

	elgg_set_ignore_access($ia);
}

function hj_forum_1359738428() {

	$ia = elgg_set_ignore_access(true);

	$subtypes[] = get_subtype_id('object', 'hjforum');
	$subtypes[] = get_subtype_id('object', 'hjforumtopic');
	$subtypes[] = get_subtype_id('object', 'hjforumpost');

	$subtypes_in = implode(',', $subtypes);

	$dbprefix = elgg_get_config('dbprefix');
	$query = "SELECT guid
				FROM {$dbprefix}entities e
				WHERE e.subtype IN ($subtypes_in)";
				
	$data = get_data($query);

	foreach ($data as $e) {
		hj_framework_set_ancestry($e->guid);
	}

	elgg_set_ignore_access($ia);
}

function hj_forum_1360277917() {

	$dbprefix = elgg_get_config('dbprefix');

	$query = "	UPDATE {$dbprefix}metastrings msv
				JOIN {$dbprefix}metadata md ON md.value_id = msv.id
				JOIN {$dbprefix}metastrings msn ON msn.id = md.name_id
				SET msv.string = 1
				WHERE msn.string = 'sticky' AND msv.string = 'true'	";

	update_data($query);

	elgg_delete_metadata(array(
		'metadata_names' => 'sticky',
		'metadata_values' => 'false',
		'limit' => 0
	));
}