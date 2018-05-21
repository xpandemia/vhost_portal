<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>Результаты ЕГЭ</h2>
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo HTML_Helper::setAlert(nl2br("<strong>Внимание!</strong>\nЧтобы добавить результаты ЕГЭ, нажмите \"Создать запись\".\nЧтобы добавить/изменить/удалить дисциплины ЕГЭ, нажмите \"Редактировать запись\" на нужных результатах ЕГЭ."), 'alert-warning');
		echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_Ege',
									'model_method' => 'getByUserGrid',
									'model_filter' => 'id_user',
									'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
									'grid' => 'grid',
									'controller' => EGE['ctr'],
									'action_add' => 'Add',
									'action_edit' => 'Edit',
									'action_delete' => 'DeleteConfirm',
									'home_hdr' => EGE['hdr']]);
	?>
</div>
