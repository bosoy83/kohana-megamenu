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
	
/**** type ****/
	
	$select_list = Kohana::$config->load('_megamenu.type');
	echo View_Admin::factory('form/control', array(
		'field' => 'type',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::select('type', $select_list, $orm->type, array(
			'id' => 'type_field',
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
	
/**** description ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'description',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::input('description', $orm->description, array(
			'id' => 'description_field',
			'class' => 'input-xxlarge',
		)),
	));
	
/**** image ****/
	
	echo View_Admin::factory('form/image', array(
		'field' => 'image',
		'value' => $orm->image,
		'orm_helper' => $helper_orm,
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
// 		'help_text' => '360x240px',
	));
	
/**** text ****/
	
	echo View_Admin::factory('form/control', array(
		'field' => 'text',
		'errors' => $errors,
		'labels' => $labels,
		'required' => $required,
		'controls' => Form::textarea('text', $orm->text, array(
			'id' => 'text_field',
			'class' => 'text_editor',
		)),
	));
	
?>
	<script type="text/javascript">
	$(function(){
		var controlsConfig = {
			list: {
				'#description_field': true,
				'#image_field': false,
				'#text_field': false,
				'[href="#tab-rows"]': true,
				'.js-menu-item-row-fix': true
			},
			image: {
				'#description_field': true,
				'#image_field': true,
				'#text_field': false,
				'[href="#tab-rows"]': false,
				'.js-menu-item-row-fix': false
			},
			text: {
				'#description_field': false,
				'#image_field': false,
				'#text_field': true,
				'[href="#tab-rows"]': false,
				'.js-menu-item-row-fix': false
			}
		};

		$('#type_field').on('change', function(){
			var val = $(this).val();

			if ( ! controlsConfig[val])
				return;

			for (selector in controlsConfig[val]) {
				if (controlsConfig[val][selector]) {
					if (selector.indexOf("[") === 0) {
						$(selector).parent()
							.removeClass('hidden');
					} else if (selector.indexOf(".js-menu-item") === 0) {
						$(selector).show();
					} else {
						$(selector).closest('.control-group')
							.show();
					}
				} else {
					if (selector.indexOf("[") === 0) {
						$(selector).parent()
							.addClass('hidden');
					} else if (selector.indexOf(".js-menu-item") === 0) {
						$(selector).hide();
					} else {
						$(selector).closest('.control-group')
							.hide();
					}
				}
			}
		}).change();
	});
	</script>
