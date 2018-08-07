<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления скан-копии!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DICT_SCANS['ctr'], DICT_SCANS['act'], DICT_SCANS['id'], DICT_SCANS['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
	</div>
	<?php
		// id_doc
		echo Form_Helper::setFormSelectListDB(['label' => 'Документ',
												'control' => 'id_doc',
												'class' => $data['id_doc_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_Docs',
												'model_method' => 'getAll',
												'model_field' => 'id',
												'model_field_name' => 'doc_name',
												'value' => $data['id_doc'],
												'success' => $data['id_doc_scs'],
												'error' => $data['id_doc_err']]);
		// numb
		echo Form_Helper::setFormInput(['label' => 'Номер пп',
										'control' => 'numb',
										'type' => 'text',
										'class' => $data['numb_cls'],
										'required' => 'no',
										'value' => $data['numb'],
										'success' => $data['numb_scs'],
										'error' => $data['numb_err']]);
		// scan_code
		echo Form_Helper::setFormInput(['label' => 'Код',
										'control' => 'scan_code',
										'type' => 'text',
										'class' => $data['scan_code_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['scan_code'],
										'success' => $data['scan_code_scs'],
										'error' => $data['scan_code_err']]);
		// scan_name
		echo Form_Helper::setFormInput(['label' => 'Наименование',
										'control' => 'scan_name',
										'type' => 'text',
										'class' => $data['scan_name_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['scan_name'],
										'success' => $data['scan_name_scs'],
										'error' => $data['scan_name_err']]);
		// required
		echo Form_Helper::setFormRadio(['label' => 'Обязательна',
										'control' => 'required',
										'required' => 'yes',
										'required_style' => 'StarUp',
										'radio' => [
													'no' => ['0' => 'Нет'],
													'yes' => ['1' => 'Да'],
													],
										'value' => $data['required'],
										'error' => $data['required_err']]);
		// main
		echo Form_Helper::setFormRadio(['label' => 'Основная группа',
										'control' => 'main',
										'required' => 'yes',
										'required_style' => 'StarUp',
										'radio' => [
													'no' => ['0' => 'Нет'],
													'yes' => ['1' => 'Да'],
													],
										'value' => $data['main'],
										'error' => $data['main_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(DICT_SCANS['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>

<script>
	$(document).ready(function(){
		formEvents();
	});
</script>

<script>
	// form events
	function formEvents() {
		// main change
		$('input[type=radio][name=main]').change(function() {
			if (this.value == 0) {
				$("label[for='numb']").html('Номер пп');
			} else {
				$("label[for='numb']").html('Номер пп*');
			};
		});
	}
</script>
