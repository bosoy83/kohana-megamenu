<?php defined('SYSPATH') or die('No direct access allowed.');

	$orm = $helper_orm->orm();
	$labels = $orm->labels();
	$required = $orm->required_fields();
	
/**** active ****/
	
	echo View_Admin::factory('form/checkbox', array(
		'field' => 'active',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'orm_helper' => $helper_orm,
	));
	
/**** title ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'title',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::input('title', $orm->title, array(
			'id' => 'title_field',
			'class' => 'input-xxlarge',
		)),
	));
	
/**** mobile_visibility ****/
	
	$select_list = Kohana::$config->load('_megamenu.mobile_visibility');
	echo View_Admin::factory('form/control', array(
		'field' => 'mobile_visibility',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::select('mobile_visibility', $select_list, $orm->mobile_visibility, array(
			'id' => 'mobile_visibility_field',
			'class' => 'input-xxlarge',
		)),
	));
	
/**** link ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'link',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::input('link', $orm->link, array(
			'id' => 'link_field',
			'class' => 'input-xxlarge',
		)),
	));
	
	