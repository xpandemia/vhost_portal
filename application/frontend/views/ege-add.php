<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка добавления результатов ЕГЭ!\nСвяжитесь с администратором."));
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(EGE['ctr'], EGE['act'], EGE['id'], EGE['hdr'], 0, '/images/logo_bsu_transparent.gif');
			// reg_year
			echo Form_Helper::setFormInput(['label' => 'Год сдачи',
											'control' => 'reg_year',
											'type' => 'text',
											'class' => $data['reg_year_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'value' => $data['reg_year'],
											'success' => $data['reg_year_scs'],
											'error' => $data['reg_year_err']]);
	?>
	<!-- controls -->
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить', 'Сохраняет данные результатов ЕГЭ');
				echo HTML_Helper::setHrefButton(EGE['ctr'], 'Reset', 'btn btn-danger', 'Очистить', 'Обнуляет форму ввода');
				echo HTML_Helper::setHrefButton(EGE['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>

<script>
	$(function(){
	  $("#reg_year").mask("9999");
	});
</script>
