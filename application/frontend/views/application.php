<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<h2>Заявления</h2>
	<?php
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($_SESSION[APP_CODE]['error_msg'], 'alert-danger');
	?>
	<div class="alert alert-warning">
		<strong>Внимание!</strong>
		<p>Чтобы добавить заявление, нажмите "Создать запись".</p>
		<p>Чтобы добавить/изменить/удалить направления подготовки, нажмите "Редактировать запись" на нужном заявлении.</p>
	</div>
	<?php
		echo HTML_Helper::setGridDB(['model_class' => 'common\\models\\Model_Application',
									'model_method' => 'getByUserGrid',
									'model_filter' => 'id_user',
									'model_filter_var' => $_SESSION[APP_CODE]['user_id'],
									'grid' => 'grid',
									'controller' => APP['ctr'],
									'action_add' => 'Add',
									'action_edit' => 'Edit',
									'action_delete' => 'DeleteConfirm',
									'home_hdr' => 'Заявления']);
	?>
</div>
