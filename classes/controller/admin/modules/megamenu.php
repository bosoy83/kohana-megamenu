<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Megamenu extends Controller_Admin_Front {

	protected $module_config = 'megamenu';
	protected $menu_active_item = 'modules';
	protected $title = 'Mega-menu';
	protected $sub_title = 'Mega-menu';
	
	protected $owner_list = array();
	protected $owner;
	protected $owner_config;
	
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
	
		$request = $this->request;
		
		$this->column_id = (int) $request->query('column');
		$this->template
			->bind_global('COLUMN_ID', $this->column_id);
		
		$query_controller = $request->query('controller');
		if ( ! empty($query_controller) AND is_array($query_controller)) {
			$this->controller_name = $request->query('controller');
		}
		$this->template
			->bind_global('CONTROLLER_NAME', $this->controller_name);
	
		$this->owner_list = $this->get_owner_list();
		$this->template
			->bind_global('OWNER_LIST', $this->owner_list);
		
		$this->owner = $request->query('owner');
		if (empty($this->owner)) {
			$this->owner =key($this->owner_list);
		}
		$this->template
			->bind_global('OWNER', $this->owner);
		
		$this->title = __($this->title);
		$this->sub_title = __($this->sub_title);
	}
	
	protected function get_owner()
	{
		$result = array(
			'owner' => NULL,
			'owner_id' => NULL,
		);
		if ( ! empty($this->owner)) {
			$tmp = explode('::', $this->owner);
			$result['owner'] = $tmp[0];
			$result['owner_id'] = $tmp[1];
			unset($tmp);
		}
		
		return $result;
	}
	
	protected function get_owner_list()
	{
		$list = Kohana::$config->load('megamenu.owners');
		$result = array();
		foreach ($list as $_func) {
			$_list = $_func();
			foreach ($_list as $_k => $_t) {
				$result[$_k] = $_t;
			}
		}
		
		return $result;
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
				'{OWNER}' => urlencode($this->owner),
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
								'query' => 'owner={OWNER}'
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
								'query' => 'owner={OWNER}&mode=fix',
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
					'query' => 'owner={OWNER}&column={COLUMN_ID}'.$back_url,
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
								'query' => 'owner={OWNER}&column={COLUMN_ID}'.$back_url,
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
								'query' => 'owner={OWNER}&column={COLUMN_ID}&mode=fix'.$back_url,
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
			'owner' => $this->owner,
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
