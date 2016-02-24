<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Megamenu extends Controller_Admin_Front {

	protected $module_config = 'megamenu';
	protected $menu_active_item = 'modules';
	protected $title = 'Mega-menu';
	protected $sub_title = 'Mega-menu';
	
	protected $column_id;
	
	protected $controller_name = array(
		'column' => 'megamenu_column',
		'row' => 'megamenu_row',
	);
	
	protected $injectors = array(
		'rows' => array('Injector_Megamenu_Row')
	);

	public function before()
	{
		parent::before();
	
		$request = $this->request->current();
		
		$this->column_id = (int) $request->query('column');
		$this->template
			->bind_global('COLUMN_ID', $this->column_id);
		
		$query_controller = $request->query('controller');
		if ( ! empty($query_controller) AND is_array($query_controller)) {
			$this->controller_name = $request->query('controller');
		}
		$this->template
			->bind_global('CONTROLLER_NAME', $this->controller_name);
	
		$this->title = __($this->title);
		$this->sub_title = __($this->sub_title);
	}
	
	protected function get_module_pages($module_key)
	{
		$prop_name = Kohana::$config->load('_megamenu.page_property');
		
		$helper_property = ORM_Helper::factory('page')->property_helper();
		$sub = $helper_property->search(array(
			$prop_name => 'true'
		), TRUE);
		
		return ORM::factory('page')
			->where('id', 'IN', $sub)
			->find_all();
	}
	
	protected function layout_aside()
	{
		$menu_items = array_merge_recursive(
			Kohana::$config->load('admin/aside/megamenu')->as_array(),
			$this->menu_left_ext
		);
		
		return parent::layout_aside()
			->set('menu_items', $menu_items)
			->set('replace', array(
				'{PAGE_ID}' => $this->module_page_id,
				'{COLUMN_ID}' => $this->column_id,
			));
	}

	protected function left_menu_column_add($orm)
	{
		if ($this->acl->is_allowed($this->user, $orm, 'add')) {
			$this->menu_left_add(array(
				'megamenu' => array(
					'sub' => array(
						'add' => array(
							'title' => __('Add column'),
							'link' => Route::url('modules', array(
								'controller' => $this->controller_name['column'],
								'action' => 'edit',
								'query' => 'page={PAGE_ID}'
							)),
						),
					),
				),
			));
		}
	}
	
	protected function left_menu_column_fix($orm)
	{
		$can_fix_all = $this->acl->is_allowed($this->user, $orm, 'fix_all');
		if ($can_fix_all) {
			$this->menu_left_add(array(
				'megamenu' => array(
					'sub' => array(
						'fix' => array(
							'title' => __('Fix positions'),
							'link' => Route::url('modules', array(
								'controller' => $this->controller_name['column'],
								'action' => 'position',
								'query' => 'page={PAGE_ID}&mode=fix',
							)),
						),
					),
				),
			));
		}
	}
	
	protected function left_menu_row_list()
	{
		$back_url = '';
		if ( ! empty($this->back_url)) {
			$back_url = '&back_url='.urlencode($this->back_url);
		}
		
		$this->menu_left_add(array(
			'megamenu_row' => array(
				'title' => __('Row list'),
				'link' => Route::url('modules', array(
					'controller' => $this->controller_name['row'],
					'query' => 'page={PAGE_ID}&column={COLUMN_ID}'.$back_url,
				)),
				'sub' => array()
			),
		));
	}
	
	protected function left_menu_row_add($orm)
	{
		if ($this->acl->is_allowed($this->user, $orm, 'add')) {
			
			$back_url = '';
			if ( ! empty($this->back_url)) {
				$back_url = '&back_url='.urlencode($this->back_url);
			}
			
			$this->menu_left_add(array(
				'megamenu_row' => array(
					'sub' => array(
						'add' => array(
							'title' => __('Add row'),
							'link' => Route::url('modules', array(
								'controller' => $this->controller_name['row'],
								'action' => 'edit',
								'query' => 'page={PAGE_ID}&column={COLUMN_ID}'.$back_url,
							)),
						),
					),
				),
			));
		}
	}
	
	protected function left_menu_row_fix($orm)
	{
		$can_fix_all = $this->acl->is_allowed($this->user, $orm, 'fix_all');
		if ($can_fix_all) {
			
			$back_url = '';
			if ( ! empty($this->back_url)) {
				$back_url = '&back_url='.urlencode($this->back_url);
			}
			
			$this->menu_left_add(array(
				'megamenu_row' => array(
					'sub' => array(
						'fix' => array(
							'title' => __('Fix positions'),
 							'class' => 'js-menu-item-row-fix',
							'link' => Route::url('modules', array(
								'controller' => $this->controller_name['row'],
								'action' => 'position',
								'query' => 'page={PAGE_ID}&column={COLUMN_ID}&mode=fix'.$back_url,
							)),
						),
					),
				),
			));
		}
	}
	
	protected function _get_breadcrumbs()
	{
		$query_array = array(
			'page' => $this->module_page_id,
		);
		return array(
			array(
				'title' => __('Columns list'),
				'link' => Route::url('modules', array(
					'controller' => $this->controller_name['column'],
					'query' => Helper_Page::make_query_string($query_array),
				)),
			)
		);
	}
	
} 
