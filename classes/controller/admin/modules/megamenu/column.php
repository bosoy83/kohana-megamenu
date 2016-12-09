<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Megamenu_Column extends Controller_Admin_Modules_Megamenu {

	public function action_index()
	{
		$owner = $this->get_owner();
		
		$orm = ORM::factory('Megamenu_Column')
			->where('owner_id', '=', $owner['owner_id'])
			->where('owner', '=', $owner['owner']);
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count($paginator_orm->count_all());
		unset($paginator_orm);

		$list = $orm
			->paginator( $paginator )
			->find_all();
		
		$this->template
			->set_filename('modules/megamenu/column/list')
			->set('list', $list)
			->set('paginator', $paginator);
		
		$this->title = __('Columns list');
			
		$this->left_menu_column_add($orm);
		$this->left_menu_column_fix($orm);
	}

	public function action_edit()
	{
		$request = $this->request;
		
		$owner = $this->get_owner();
		$id = (int) $request->param('id');
		$helper_orm = ORM_Helper::factory('Megamenu_Column');
		$orm = $helper_orm->orm();
		if ( (bool) $id) {
			$orm
				->and_where('id', '=', $id)
				->where('owner_id', '=', $owner['owner_id'])
				->where('owner', '=', $owner['owner'])
				->find();
			
			if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->title = __('Edit column');
		} else {
			$this->title = __('Add column');
		}
		
		if (empty($this->back_url)) {
			$query_array = array(
				'owner' => $this->owner,
			);
			$query_array = Paginator::query($request, $query_array);
			$this->back_url = Route::url('modules', array(
				'controller' => $this->controller_name['column'],
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
					$orm->owner_id = $owner['owner_id'];
					$orm->owner = $owner['owner'];
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
			
			if ($orm->loaded()) {
				$this->column_id = (int) $orm->id;
			}
			
			$this->template
				->set_filename('modules/megamenu/column/edit')
				->set('helper_orm', $helper_orm);
			
			$this->left_menu_column_add($orm);
			
			$injector = $this->injectors['rows'];
			if ($orm->loaded()) {
				try {
					$this->hook_list_content[] = $injector->get_hook($this->owner, $orm->id);
			
					$this->menu_left_add( $injector->menu_list() );
					$this->menu_left_add( $injector->menu_add($orm->rows) );
					$this->menu_left_add( $injector->menu_fix($orm->rows) );
			
				} catch (ORM_Validation_Exception $e) {
					$errors = array_merge($errors, $this->errors_extract($e));
				}
			}
			
			$this->template
				->set('errors', $errors);
				
		} else {
			$request
				->redirect($this->back_url);
		}
	}

	public function action_delete()
	{
		$request = $this->request;
		
		$owner = $this->get_owner();
		$id = (int) $request->param('id');
	
		$helper_orm = ORM_Helper::factory('Megamenu_Column');
		$orm = $helper_orm->orm();
		$orm
			->and_where('id', '=', $id)
			->where('owner_id', '=', $owner['owner_id'])
			->where('owner', '=', $owner['owner'])
			->find();
	
		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'edit')) {
			throw new HTTP_Exception_404();
		}
	
		if ($this->element_delete($helper_orm)) {
			if (empty($this->back_url)) {
				$query_array = array(
					'owner' => $this->owner,
				);
				$this->back_url = Route::url('modules', array(
					'controller' => $this->controller_name['column'],
					'query' => Helper_Page::make_query_string($query_array),
				));
			}
		
			$request
				->redirect($this->back_url);
		}
	}
	
	public function action_position()
	{
		$request = $this->request;
		$id = (int) $request->param('id');
		$mode = $request->query('mode');
		$errors = array();
		$helper_orm = ORM_Helper::factory('Megamenu_Column');

		try {
			$this->element_position($helper_orm, $id, $mode);
		} catch (ORM_Validation_Exception $e) {
			$errors = $this->errors_extract($e);
		}
		
		if (empty($errors)) {
			if (empty($this->back_url)) {
				$query_array = array(
					'owner' => $this->owner,
				);
				if ($mode != 'fix') {
					$query_array = Paginator::query($request, $query_array);
				}
		
				$this->back_url = Route::url('modules', array(
					'controller' => $this->controller_name['column'],
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
	
		$request = $this->request;
		if (in_array($request->action(), array('edit'))) {
				
			$id = (int) $request->param('id');
			$orm = ORM::factory('Megamenu_Column')
				->where('id', '=', $id)
				->find();
			
			if ($orm->loaded()) {
				$breadcrumbs[] = array(
					'title' => $orm->title.' ['.__('column edition').']',
				);
			} else {
				$breadcrumbs[] = array(
					'title' => ' ['.__('new column').']',
				);
			}
		}
	
		return $breadcrumbs;
	}
	
} 
