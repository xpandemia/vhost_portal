<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка дисциплин ЕГЭ!\nСвяжитесь с администратором."));
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>Дисциплины ЕГЭ</h2>
	<?php
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
		echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_EgeDisciplines',
									'model_method' => 'getGrid',
									'model_filter' => 'pid',
									'model_filter_var' => $data['pid'],
									'grid' => 'grid',
									'controller' => EGE_DSP['ctr'],
									'action_add' => 'Add/?pid='.$data['pid'],
									'action_edit' => 'Edit',
									'action_delete' => 'DeleteConfirm',
									'home_hdr' => 'Дисциплины ЕГЭ']);
	?>
</div>
