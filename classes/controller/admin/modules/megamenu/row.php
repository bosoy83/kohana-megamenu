<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Megamenu_Row extends Controller_Admin_Modules_Megamenu {
	
	private $column;
	
	public function before()
	{
		parent::before();
		
		$owner = $this->get_owner();
		
		$this->column = ORM::factory('Megamenu_Column')
			->and_where('id', '=', $this->column_id)
			->where('owner_id', '=', $owner['owner_id'])
			->where('owner', '=', $owner['owner'])
			->find();
		
		if ( ! $this->column->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$this->sub_title = $this->column->title;
	}
	
	public function action_index()
	{
		$orm = ORM::factory('Megamenu_Row')
			->where('column_id', '=', $this->column_id);

		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count($paginator_orm->count_all());
		unset($paginator_orm);

		$list = $orm
			->paginator($paginator)
			->find_all();
		
		$this->template
			->set_filename('modules/megamenu/row/list')
			->set('list', $list)
			->set('paginator', $paginator);
		
		$this->title = __('Columns list');
		
		$this->left_menu_column_add($this->column);
		$this->left_menu_row_list();
		$this->left_menu_row_add($orm);
		$this->left_menu_row_fix($orm);
	}

	public function action_edit()
	{
		$request = $this->request;
		
		$id = (int) $request->param('id');
		$helper_orm = ORM_Helper::factory('Megamenu_Row');
		$orm = $helper_orm->orm();
		if ( (bool) $id) {
			$orm
				->and_where('id', '=', $id)
				->and_where('column_id', '=', $this->column_id)
				->find();
			if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->title = __('Edit row');
		} else {
			$this->title = __('Add row');
		}
		
		if (empty($this->back_url)) {
			$query_array = array(
				'owner' => $this->owner,
				'column' => $this->column_id,
			);
			$query_array = Paginator::query($request, $query_array);
			$this->back_url = Route::url('modules', array(
				'controller' => $this->controller_name['row'],
				'query' => Helper_Page::make_query_string($query_array),
			));
		}
		
		if ($this->is_cancel) {
			$request
				->redirect($this->back_url);
		}

		$errors = array();
		$submit = $request->post('submit');
		if ($submit) {
			try {
				if ( (bool) $id) {
					$orm->updater_id = $this->user->id;
					$orm->updated = date('Y-m-d H:i:s');
					$reload = FALSE;
				} else {
					$orm->creator_id = $this->user->id;
					$orm->column_id = $this->column_id;
					$reload = TRUE;
				}
			
				$values = $request->post();
			
				$helper_orm->save($values + $_FILES);
				
				if ($reload) {
					if ($submit != 'save_and_exit') {
						$this->back_url = Route::url('modules', array(
							'controller' => $request->controller(),
							'action' => $request->action(),
							'id' => $orm->id,
							'query' => Helper_Page::make_query_string($request->query()),
						));
					}
				
					$request
						->redirect($this->back_url);
				}
			} catch (ORM_Validation_Exception $e) {
				$errors = $this->errors_extract($e);
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			
			$this->template
				->set_filename('modules/megamenu/row/edit')
				->set('errors', $errors)
				->set('helper_orm', $helper_orm);
			
			$this->left_menu_column_add($this->column);
			$this->left_menu_row_list();
			$this->left_menu_row_add($orm);
			$this->left_menu_row_fix($orm);
			
		} else {
			$request
				->redirect($this->back_url);
		}
	}

	public function action_delete()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
	
		$helper_orm = ORM_Helper::factory('Megamenu_Row');
		$orm = $helper_orm->orm();
		$orm
			->and_where('id', '=', $id)
			->and_where('column_id', '=', $this->column_id)
			->find();
	
		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
			throw new HTTP_Exception_404();
		}
	
		if ($this->element_delete($helper_orm)) {
			if (empty($this->back_url)) {
				$query_array = array(
					'owner' => $this->owner,
					'column' => $this->column_id,
				);
				$this->back_url = Route::url('modules', array(
					'controller' => $this->controller_name['row'],
					'query' => Helper_Page::make_query_string($query_array),
				));
			}
		
			$request
				->redirect($this->back_url);
		}
	}
	
	public function action_position()
	{
		$request = $this->request->current();
		$id = (int) $request->param('id');
		$mode = $request->query('mode');
		$errors = array();
		$helper_orm = ORM_Helper::factory('Megamenu_Row');
		
		try {
			$this->element_position($helper_orm, $id, $mode);
		} catch (ORM_Validation_Exception $e) {
			$errors = $this->errors_extract($e);
		}
		
		if (empty($errors)) {
			if (empty($this->back_url)) {
				$query_array = array(
					'owner' => $this->owner,
					'column' => $this->column_id,
				);
				if ($mode != 'fix') {
					$query_array = Paginator::query($request, $query_array);
				}
		
				$this->back_url = Route::url('modules', array(
					'controller' => $this->controller_name['row'],
					'query' => Helper_Page::make_query_string($query_array),
				));
			}
		
			$request
				->redirect($this->back_url);
		}
	}
	
	protected function _get_breadcrumbs()
	{
		$breadcrumbs = parent::_get_breadcrumbs();
		
		$query_array = array(
			'owner' => $this->owner,
		);
		$breadcrumbs[] = array(
			'title' => $this->column->title,
			'link' => Route::url('modules', array(
				'controller' => $this->controller_name['column'],
				'action' => 'edit',
				'id' => $this->column->id,
				'query' => Helper_Page::make_query_string($query_array),
			))
		);
		
		$request = $this->request;
		if (in_array($request->action(), array('edit'))) {
			$id = (int) $request->param('id');
			$orm = ORM::factory('Megamenu_Row')
				->where('id', '=', $id)
				->find();
			if ($orm->loaded()) {
				$breadcrumbs[] = array(
					'title' => $orm->title.' ['.__('row edition').']',
				);
			} 
		} elseif (in_array($request->action(), array('index'))) {
			$breadcrumbs[] = array(
				'title' => __('Row list'),
			);
		}
		
		return $breadcrumbs;
	}
} 
