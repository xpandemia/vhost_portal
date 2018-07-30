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
						echo '<div class=""><legend class="font-weight-bold">Найдены пользователи</legend></div>';
					}
				?>
				<div class="col">
					<input type="text" placeholder="Найти..." id="search" name="search">
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
		echo '<table class="table table-bordered table-hover">';
		echo '<thead class="thead-dark">';
		echo '<tr>';
		foreach ($user->grid() as $key => $value) {
			echo '<th class="align-text-top">'.$value['name'].'</th>';
		}
		echo '</tr>';
		echo '</thead>';
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
			echo '<tbody>';
			$i = 0;
			$id_min = 0;
			$id_max = 0;
			foreach ($user_arr as $user_row) {
				echo '<tr>';
				echo '<td>'.$user_row['id'].'</td>';
				echo '<td>'.$user_row['username'].'</td>';
				echo '<td>'.$user_row['email'].'</td>';
				echo '<td>'.$user_row['role'].'</td>';
				echo '<td>'.$user_row['status'].'</td>';
				echo '<td>'.$user_row['dt_created'].'</td>';
				echo '<td>';
				echo HTML_Helper::setHrefButtonIcon(USER['ctr'], 'Mask/?id='.$user_row['id'], 'font-weight-bold', 'fas fa-user-secret fa-2x', 'Войти как');
				echo HTML_Helper::setHrefButtonIcon(USER['ctr'], 'Edit/?id='.$user_row['id'], 'font-weight-bold', 'far fa-edit fa-2x', 'Редактировать');
				echo HTML_Helper::setHrefButtonIcon(USER['ctr'], 'DeleteConfirm/?id='.$user_row['id'].'&hdr=Пользователи&ctr='.USER['ctr'], 'text-danger font-weight-bold', 'fas fa-times fa-2x', 'Удалить');
				echo '</td>';
				echo '</tr>';
				if ($i === 0) {
					$id_min = $user_row['id'];
				} else {
					$id_max = $user_row['id'];
				}
				$i++;
			}
			echo '</tbody>';
		}
		echo '</table>';
		// set pagination
		if (isset($data['step'])) {
			echo HTML_Helper::setPagination(['model_class' => 'common\\models\\Model_User',
											'model_data_method' => 'getPages',
											'model_count_method' => 'getPagesCount',
											'model_filter' => 'id',
											'model_filter_var' => $data['id'],
											'page' => $data['page'],
											'id' => $id_min,
											'rows' => $user::LIMIT_ROWS]);
		}
	?>
</div>
