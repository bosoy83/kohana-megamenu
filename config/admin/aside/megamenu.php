<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'megamenu' => array(
		'title' => __('Columns list'),
		'link' => Route::url('modules', array(
			'controller' => 'megamenu_column',
			'query' => 'page={PAGE_ID}',
		)),
		'sub' => array(),
	),
    'megamenu_row' => array(),
);