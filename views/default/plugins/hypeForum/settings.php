<?php

$instance_label = elgg_echo('hj:forum:admin:instance');
$instance_input = elgg_view('input/dropdown', array(
	'name' => 'params[instance_handling]',
	'value' => $vars['entity']->instance_handling,
	'options_values' => array(
		'menu' => elgg_echo('hj:forum:admin:instance:menu'),
		'list' => elgg_echo('hj:forum:admin:instance:list')
	)
));

$settings = <<<__HTML

	<h3>Display Settings</h3>
    <div>
        <p><i>$instance_label</i><br>$instance_input</p>
    </div>
    <hr>

</div>
__HTML;

echo $settings;