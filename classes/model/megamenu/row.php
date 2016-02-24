<?php defined('SYSPATH') or die('No direct script access.');

class Model_Megamenu_Row extends ORM_Base {

	protected $_table_name = 'megamenu_rows';
	protected $_sorting = array('position' => 'ASC');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	protected $_belongs_to = array(
		'column' => array(
			'model' => 'megamenu_Column',
			'foreign_key' => 'column_id',
		),
	);
	
	public function labels()
	{
		return array(
			'title'             => 'Title',
			'mobile_visibility' => 'Mobile visibility',
			'link'              => 'Link',
			'active'            => 'Active',
			'position'          => 'Position',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array('digit'),
			),
			'column_id' => array(
				array('not_empty'),
				array('digit'),
			),
			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
			),
			'mobile_visibility' => array(
				array('not_empty'),
				array('in_array', array(':value', array('show', 'hide'))),
			),
			'link' => array(
				array('max_length', array( ':value', 255)),
			),
			'position' => array(
				array('digit'),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'trim' ),
			),
			'title' => array(
				array( 'strip_tags' ),
			),
			'link' => array(
				array( 'strip_tags' ),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
		);
	}
}
