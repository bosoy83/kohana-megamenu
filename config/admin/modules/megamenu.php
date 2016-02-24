<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'a2' => array(
		'resources' => array(
			'megamenu_column_controller' => 'module_controller',
			'megamenu_row_controller' => 'module_controller',
			'megamenu_column' => 'module',
			'megamenu_row' => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access_1' => array(
					'role' => 'main',
					'resource' => 'megamenu_column_controller',
					'privilege' => 'access',
				),
				'controller_access_2' => array(
					'role' => 'main',
					'resource' => 'megamenu_row_controller',
					'privilege' => 'access',
				),
				
				'megamenu_column_add' => array(
					'role' => 'main',
					'resource' => 'megamenu_column',
					'privilege' => 'add',
				),
				'megamenu_column_edit' => array(
					'role' => 'main',
					'resource' => 'megamenu_column',
					'privilege' => 'edit',
				),
				'megamenu_column_fix' => array(
					'role' => 'main',
					'resource' => 'megamenu_column',
					'privilege' => 'fix_all',
				),
				
				'megamenu_row_add' => array(
					'role' => 'main',
					'resource' => 'megamenu_row',
					'privilege' => 'add',
				),
				'megamenu_row_edit' => array(
					'role' => 'main',
					'resource' => 'megamenu_row',
					'privilege' => 'edit',
				),
				'megamenu_row_fix' => array(
					'role' => 'main',
					'resource' => 'megamenu_row',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);