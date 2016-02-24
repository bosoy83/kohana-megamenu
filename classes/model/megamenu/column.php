<?php defined('SYSPATH') or die('No direct script access.');

class Model_Megamenu_Column extends ORM_Base {

	protected $_table_name = 'megamenu_columns';
	protected $_sorting = array('position' => 'ASC');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	protected $_has_many = array(
		'rows' => array(
			'model' => 'megamenu_Row',
			'foreign_key' => 'column_id',
		),
	);
	
	public function labels()
	{
		return array(
			'title' => 'Title',
			'mobile_visibility' => 'Mobile visibility',
			'link' => 'Link',
			'description' => 'Description',
			'image' => 'Image',
			'active' => 'Active',
			'text' => 'Text',
			'type' => 'Type',
			'position' => 'Position',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array('digit'),
			),
			'page_id' => array(
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
			'image' => array(
				array('max_length', array(':value', 255)),
			),
			'description' => array(
				array('max_length', array( ':value', 255)),
			),
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array('list', 'image', 'text'))),
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
			'description' => array(
				array( 'strip_tags' ),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
		);
	}
}
