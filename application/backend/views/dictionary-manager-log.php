<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_DictionaryManager as DictionaryManager;
use common\models\Model_DictionaryManagerLog as DictionaryManagerLog;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка истории управления справочниками!');
	} else {
		$dm = new DictionaryManager();
		$dm->id = $data['id'];
		$dm_row = $dm->getById();
	}
?>
<div class="container-fluid rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<div class="rounded bg-dark text-light sticky_top">
		<div class="row">
			<div class="">
				<?php echo HTML_Helper::setHrefButton(DICT_MANAGER['ctr'], 'Index', 'btn btn-success', 'Вернуться'); ?>
			</div>
			<div class="col">
				<h2>История управления справочником "<?php echo $dm_row['dict_name']; ?>"</h2>
			</div>
		</div>
	</div>
	<br>
	<?php
		$log = new DictionaryManagerLog();
		echo HTML_Helper::setTableBegin();
		echo HTML_Helper::setTableHeader(['class' => 'thead-dark',
										'grid' => $log->grid()]);
		$log->id_dict = $data['id'];
		$log_arr = $log->getGridByDict();
		if ($log_arr) {
			echo '<tbody>';
			foreach ($log_arr as $log_row) {
				echo HTML_Helper::setTableRow(['grid' => $log->grid(),
												'row' => $log_row]);
			}
			echo '</tbody>';
		}
		echo HTML_Helper::setTableEnd();
	?>
</div>
