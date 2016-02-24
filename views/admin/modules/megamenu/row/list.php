<?php defined('SYSPATH') or die('No direct access allowed.');

	if (Request::current()->is_initial()) {
		echo View_Admin::factory('layout/breadcrumbs', array(
			'breadcrumbs' => $breadcrumbs
		));
	}

	if ($list->count() <= 0) {
		return;
	}
	
	$query_array = array(
		'page' => $MODULE_PAGE_ID,
		'column' => $COLUMN_ID,
	);
	if ( ! empty($BACK_URL)) {
		$query_array['back_url'] = $BACK_URL;
	}
	
	$query_array = Paginator::query(Request::current(), $query_array);
	$edit_tpl = Route::url('modules', array(
		'controller' => $CONTROLLER_NAME['row'],
		'action' => 'edit',
		'id' => '{id}',
		'query' => Helper_Page::make_query_string($query_array),
	));
	$delete_tpl	= Route::url('modules', array(
		'controller' => $CONTROLLER_NAME['row'],
		'action' => 'delete',
		'id' => '{id}',
		'query' => Helper_Page::make_query_string($query_array),
	));

	
	$query_array['mode'] = 'first';
	$first_tpl = Route::url('modules', array(
		'controller' => $CONTROLLER_NAME['row'],
		'action' => 'position',
		'id' => '{id}',
		'query' => Helper_Page::make_query_string($query_array),
	));
	$query_array['mode'] = 'up';
	$up_tpl	= Route::url('modules', array(
		'controller' => $CONTROLLER_NAME['row'],
		'action' => 'position',
		'id' => '{id}',
		'query' => Helper_Page::make_query_string($query_array),
	));
	$query_array['mode'] = 'down';
	$down_tpl = Route::url('modules', array(
		'controller' => $CONTROLLER_NAME['row'],
		'action' => 'position',
		'id' => '{id}',
		'query' => Helper_Page::make_query_string($query_array),
	));
	$query_array['mode'] = 'last';
	$last_tpl = Route::url('modules', array(
		'controller' => $CONTROLLER_NAME['row'],
		'action' => 'position',
		'id' => '{id}',
		'query' => Helper_Page::make_query_string($query_array),
	));
?>
	<table class="table table-bordered table-striped">
		<colgroup>
			<col class="span1">
			<col class="span6">
			<col class="span2">
		</colgroup>
		<thead>
			<tr>
				<th><?php echo __('ID'); ?></th>
				<th><?php echo __('Title'); ?></th>
				<th><?php echo __('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php 
		foreach ($list as $_orm):
?>
			<tr>
				<td><?php echo $_orm->id ?></td>
				<td>
<?php
					if ( (bool) $_orm->active) {
						echo '<i class="icon-eye-open"></i>&nbsp;';
					} else {
						echo '<i class="icon-eye-open" style="background: none;"></i>&nbsp;';
					}
					echo HTML::chars($_orm->title);
?>
				</td>
				<td>
<?php 
				if ($ACL->is_allowed($USER, $_orm, 'edit')) {
					echo '<div class="btn-group">';
				
						echo HTML::anchor(str_replace('{id}', $_orm->id, $edit_tpl), '<i class="icon-edit"></i> '.__('Edit'), array(
							'class' => 'btn',
							'title' => __('Edit'),
						));
				
						echo '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
						echo '<ul class="dropdown-menu">';
							
							echo View_Admin::factory('layout/controls/position', array(
								'orm' => $_orm,
								'first_tpl' => $first_tpl,
								'up_tpl' => $up_tpl,
								'down_tpl' => $down_tpl,
								'last_tpl' => $last_tpl,
							));
								
							echo '<li>', HTML::anchor(str_replace('{id}', $_orm->id, $delete_tpl), '<i class="icon-remove"></i> '.__('Delete'), array(
								'class' => 'delete_button',
								'title' => __('Delete'),
							)), '</li>';
							
						echo '</ul>';
					echo '</div>';
				}
?>
				</td>
			</tr>
<?php 
		endforeach;
?>
		</tbody>
	</table>
<?php
	if (empty($BACK_URL)) {
		$query_array = array(
			'page' => $MODULE_PAGE_ID,
			'column' => $COLUMN_ID,
		);
		$link = Route::url('modules', array(
			'controller' => $CONTROLLER_NAME['row'],
			'query' => Helper_Page::make_query_string($query_array),
		));
	} else {
		$link = $BACK_URL;
	}
	
	echo $paginator->render($link);