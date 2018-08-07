<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления документа!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DOCS['ctr'], DOCS['act'], DOCS['id'], DOCS['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
	</div>
	<?php
		// doc_code
		echo Form_Helper::setFormInput(['label' => 'Код',
										'control' => 'doc_code',
										'type' => 'text',
										'class' => $data['doc_code_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['doc_code'],
										'success' => $data['doc_code_scs'],
										'error' => $data['doc_code_err']]);
		// doc_name
		echo Form_Helper::setFormInput(['label' => 'Наименование',
										'control' => 'doc_name',
										'type' => 'text',
										'class' => $data['doc_name_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['doc_name'],
										'success' => $data['doc_name_scs'],
										'error' => $data['doc_name_err']]);
		// table_name
		echo Form_Helper::setFormInput(['label' => 'Наименование таблицы',
										'control' => 'table_name',
										'type' => 'text',
										'class' => $data['table_name_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['table_name'],
										'success' => $data['table_name_scs'],
										'error' => $data['table_name_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(DOCS['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
