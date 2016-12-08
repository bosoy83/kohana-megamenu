<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'owners' => array(
		function(){
			$helper_property = ORM_Helper::factory('Page')->property_helper();
			$sub = $helper_property->search(array(
				'PageMegamenu' => 'true'
			), TRUE);
			
			$list = ORM::factory('page')
				->where('id', 'IN', $sub)
				->find_all();
			
			$result = array();
			foreach ($list as $_orm) {
				$result['Page::'.$_orm->id] = __('Page').' "'.$_orm->title.'"';
			}
			
			return $result;
		}
	),
);