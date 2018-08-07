<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_DictionaryManager as DictionaryManager;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="container-fluid rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<div class="rounded bg-dark text-light sticky_top">
		<div class="row">
			<?php
				echo '<div class="">';
				echo '<h2>'.DICT_MANAGER['hdr'].'</h2>';
				echo '</div>';
				echo '<div class="col col-sm-2">';
				echo HTML_Helper::setHrefButtonIcon(DICT_MANAGER['ctr'], 'Sync', 'btn btn-primary', 'fas fa-sync', 'Обновить');
				echo '</div>';
			?>
		</div>
			<?php
				echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
				echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
			?>
	</div>
	<br>
	<?php
		$dm = new DictionaryManager();
		echo HTML_Helper::setHrefButtonIcon(DICT_MANAGER['ctr'], 'Add', 'font-weight-bold', 'far fa-file fa-2x', 'Создать');
		echo HTML_Helper::setTableBegin();
		echo HTML_Helper::setTableHeader(['class' => 'thead-dark',
										'grid' => $dm->grid()]);
		$dm_arr = $dm->getGrid();
		if ($dm_arr) {
			echo '<tbody>';
			foreach ($dm_arr as $dm_row) {
				echo HTML_Helper::setTableRow(['grid' => $dm->grid(),
												'row' => $dm_row,
												'controls' => [
																['controller' => DICT_MANAGER['ctr'], 'action' => 'Log/?id='.$dm_row['id'], 'class' => 'font-weight-bold', 'icon' => 'fas fa-history fa-2x', 'tooltip' => 'История'],
																['controller' => DICT_MANAGER['ctr'], 'action' => 'Edit/?id='.$dm_row['id'], 'class' => 'font-weight-bold', 'icon' => 'far fa-edit fa-2x', 'tooltip' => 'Редактировать'],
																['controller' => DICT_MANAGER['ctr'], 'action' => 'DeleteConfirm/?id='.$dm_row['id'].'&hdr=Управление справочниками&ctr='.DICT_MANAGER['ctr'], 'class' => 'text-danger font-weight-bold', 'icon' => 'fas fa-times fa-2x', 'tooltip' => 'Удалить']
																]]);
			}
			echo '</tbody>';
		}
		echo HTML_Helper::setTableEnd();
	?>
</div>
