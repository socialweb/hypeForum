<?php

if (elgg_in_context('activity') || elgg_in_context('widgets') || elgg_in_context('print')) {
	return true;
}

echo elgg_view('framework/bootstrap/entity/menu', $vars);
