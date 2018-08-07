<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_User as User;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, APP['ctr'], 'Index', null, nl2br("Ошибка пользователей!\nСвяжитесь с администратором."));
	}
?>
<div class="container-fluid rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<div class="search-container">
		<form action="<?php echo Basic_Helper::appUrl(USER['ctr'], 'Search'); ?>" method="post" id="form_user_search" novalidate>
			<div class="form-group row">
				<?php
					if (isset($data['step'])) {
						echo '<div class=""><legend class="font-weight-bold">Пользователи</legend></div>';
					} else {
						echo '<div class=""><legend class="font-weight-bold">Найдены пользователи: '.count($data).'</legend></div>';
					}
				?>
				<div class="col">
					<input type="text" placeholder="Найти по логину..." id="search_username" name="search_username">
					<input type="text" placeholder="Найти по эл. почте..." id="search_email" name="search_email">
					<button type="submit" data-toggle="tooltip" title="Искать"><i class="fa fa-search"></i></button>
				</div>
			</div>
		</form>
	</div>
	<?php
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
		$user = new User();
		echo HTML_Helper::setHrefButtonIcon(USER['ctr'], 'Add', 'font-weight-bold', 'far fa-file fa-2x', 'Создать');
		echo HTML_Helper::setTableBegin();
		echo HTML_Helper::setTableHeader(['class' => 'thead-dark',
										'grid' => $user->grid()]);
		if (isset($data['step'])) {
			$user->id = $data['id'];
			if ($data['step'] == 'prev') {
				$user_arr = array_reverse($user->getPagePrev());
			} else {
				$user_arr = $user->getPageNext();
			}
		} else {
			$user_arr = $data;
		}
		if ($user_arr) {
			$i = 0;
			$id_min = 0;
			$id_max = 0;
			echo '<tbody>';
			foreach ($user_arr as $user_row) {
				echo HTML_Helper::setTableRow(['grid' => $user->grid(),
												'row' => $user_row,
												'controls' => [
																['controller' => USER['ctr'], 'action' => 'Mask/?id='.$user_row['id'], 'class' => 'font-weight-bold', 'icon' => 'fas fa-user-secret fa-2x', 'tooltip' => 'Войти как'],
																['controller' => USER['ctr'], 'action' => 'Edit/?id='.$user_row['id'], 'class' => 'font-weight-bold', 'icon' => 'far fa-edit fa-2x', 'tooltip' => 'Редактировать'],
																['controller' => USER['ctr'], 'action' => 'DeleteConfirm/?id='.$user_row['id'].'&hdr=Пользователи&ctr='.USER['ctr'], 'class' => 'text-danger font-weight-bold', 'icon' => 'fas fa-times fa-2x', 'tooltip' => 'Удалить']
																]]);
				if ($i === 0) {
					$id_min = $user_row['id'];
					$id_max = $user_row['id'];
				} else {
					$id_max = $user_row['id'];
				}
				$i++;
			}
			echo '</tbody>';
		}
		echo HTML_Helper::setTableEnd();
		// set pagination
		if (isset($data['step'])) {
			echo HTML_Helper::setPagination(['model_class' => 'common\\models\\Model_User',
											'model_data_method' => 'getPages',
											'model_page_method' => 'getPageNumber',
											'model_count_method' => 'getPagesCount',
											'model_rowsless_method' => 'getRowsCountLess',
											'model_rows_method' => 'getRowsCount',
											'model_filter' => 'id',
											'model_filter_var' => $data['id'],
											'id' => $id_min,
											'rows' => $user::LIMIT_ROWS]);
		}
	?>
</div>
