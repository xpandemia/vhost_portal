<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>ЕГЭ</h2>
	<div class="alert alert-warning">
		<strong>Внимание!</strong>
		<p>Чтобы добавить документ ЕГЭ, нажмите "Создать запись".</p>
		<p>Чтобы добавить/изменить/удалить дисциплины ЕГЭ, нажмите "Редактировать запись" на нужном документе ЕГЭ.</p>
	</div>
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
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
