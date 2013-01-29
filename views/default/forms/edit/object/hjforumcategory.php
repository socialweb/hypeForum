<?php

$vars['form_name'] = $form_name = 'edit:object:hjforumcategory';

$config = elgg_get_config('framework:config:forms');
$vars['form'] = $config[$form_name];

echo elgg_view('framework/bootstrap/form', $vars);