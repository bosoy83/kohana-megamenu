<?php defined('SYSPATH') or die('No direct script access.');

class Injector_Megamenu_Row extends Injector_Base {
	
	private $controller_name = 'megamenu_row';
	private $tab_code = 'tab-rows';
	
	protected function init() {
		$module_config = Helper_Module::load_config('megamenu');
		$helper_acl = new Helper_ACL($this->acl);
		$helper_acl->inject(Arr::get($module_config, 'a2'));
	}
	
	public function get_hook($page_id, $column_id)
	{
		return array(
			array($this, 'hook_callback'),
			array($page_id, $column_id)
		);
	}
	
	public function hook_callback($content, $page_id, $column_id)
	{
		$request = $this->request;
		$back_url = $request->url();
		$query_array = $request->query();
		if ( ! empty($query_array)) {
			$back_url .= '?'.http_build_query($query_array);
		}
		$back_url .= '#tab-'.$this->tab_code;
		unset($query_array);
	
		$query_array = array(
			'page' => $page_id,
			'column' => $column_id,
			'back_url' => $back_url,
			'content_only' => TRUE
		);
		$query_array = Paginator::query($request, $query_array);
		$link = Route::url('modules', array(
			'controller' => $this->controller_name,
			'query' => Helper_Page::make_query_string($query_array),
		));
		
		$html = Request::factory($link)
			->execute()
			->body();
	
		$tab_nav_html = View_Admin::factory('layout/tab/nav', array(
			'code' => $this->tab_code,
			'title' => '<b>'.__('Rows').'</b>',
		));
		$tab_pane_html = View_Admin::factory('layout/tab/pane', array(
			'code' => $this->tab_code,
			'content' => $html
		));
	
		return str_replace(array(
			'<!-- #tab-nav-insert# -->', '<!-- #tab-pane-insert# -->'
		), array(
			$tab_nav_html.'<!-- #tab-nav-insert# -->', $tab_pane_html.'<!-- #tab-pane-insert# -->'
		), $content);
	}
	
	public function menu_list($tab_mode = TRUE)
	{
		if ($tab_mode) {
			$link = '#tab-'.$this->tab_code;
			$class = 'tab-control';
		} else {
			$link = Route::url('modules', array(
				'controller' => $this->controller_name['row'],
				'query' => 'page={PAGE_ID}&column={COLUMN_ID}'.$back_url,
			));
			$class = FALSE;
		}
		
		return array(
			'megamenu_row' => array(
				'title' => __('Row list'),
				'link' => $link,
				'class' => $class,
				'sub' => array(),
			),
		);
	}
	
	public function menu_add($orm)
	{
		if ($this->acl->is_allowed($this->user, $orm, 'add')) {
			$back_url = urlencode($_SERVER['REQUEST_URI'].'#tab-'.$this->tab_code);
	
			return array(
				'megamenu_row' => array(
					'sub' => array(
						'add' => array(
							'title' => __('Add row'),
							'link' => Route::url('modules', array(
								'controller' => $this->controller_name,
								'action' => 'edit',
								'query' => 'page={PAGE_ID}&column={COLUMN_ID}&back_url='.$back_url,
							)),
						),
					),
				),
			);
		}
	}
	
	public function menu_fix($orm)
	{
		$can_fix_all = $this->acl->is_allowed($this->user, $orm, 'fix_all');
	
		if ($can_fix_all) {
			$back_url = urlencode($_SERVER['REQUEST_URI'].'#tab-'.$this->tab_code);
	
			return array(
				'megamenu_row' => array(
					'sub' => array(
						'fix' => array(
							'title' => __('Fix positions'),
 							'class' => 'js-menu-item-row-fix',
							'link' => Route::url('modules', array(
								'controller' => $this->controller_name['row'],
								'action' => 'position',
								'query' => 'page={PAGE_ID}&column={COLUMN_ID}&mode=fix&back_url='.$back_url,
							)),
						),
					),
				),
			);
		}
	}
	
}