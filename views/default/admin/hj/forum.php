<?php
/**
 * Renders hypeFormBuilder Admin Interface
 * 
 * @package hypeJunction
 * @subpackage hypeFormBuilder
 * @category Forms
 * @category Admin Interface
 * 
 * @uses int hjForm::$guid     NULL|INT GUID of an hjForm we are trying to edit
 * @return string HTML
 */
?><?php

// Load libraries that may be required

elgg_load_js('hj.framework.ajax');
elgg_load_js('hj.framework.fieldcheck');

elgg_load_js('hj.formbuilder.base');
elgg_load_js('hj.formbuilder.sortable');
elgg_load_css('hj.formbuilder.base');

elgg_load_css('elgg.admin');

$form_guid = get_input('e');
$form = get_entity($form_guid);

$mode = get_input('mode', 'beginner');

/*
 * Left (Sidebar) HTML
 */
$sidebar_title = elgg_echo('hj:formbuilder:sidebartitle');
$sidebar = elgg_echo('hj:formbuilder:sidebardescription');
$sidebar .= elgg_view('input/dropdown', array(
    'name' => 'e',
    'value' => $form_guid,
    'options_values' => hj_formbuilder_get_forms_as_dropdown(),
        ));
$sidebar .= elgg_view('input/hidden', array(
    'name' => 'mode',
    'value' => $mode
));

$sidebar .= <<<HTML
    <div id="hj-formbuilder-container-form"></div>
HTML;

$body .= <<<HTML
    <div id="hj-formbuilder-container-fields"></div>
HTML;
/*
 * Right (Body) HTML
 */
$body = elgg_view_module('main', null, $body);
$sidebar = elgg_view_module('main', $sidebar_title, $sidebar);

/*
 * Join sidebar and body
 */
$page = elgg_view_layout('hj/dynamic', array(
        'content' => array($sidebar, $body),
        'grid' => array(5, 7)
        ));

echo $page;