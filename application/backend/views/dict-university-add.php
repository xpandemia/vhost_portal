<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления места поступления!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DICT_UNIVERSITY['ctr'], DICT_UNIVERSITY['act'], DICT_UNIVERSITY['id'], DICT_UNIVERSITY['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
	</div>
	<?php
		// code
		echo Form_Helper::setFormInput(['label' => 'Код',
										'control' => 'code',
										'type' => 'text',
										'class' => $data['code_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['code'],
										'success' => $data['code_scs'],
										'error' => $data['code_err']]);
		// description
		echo Form_Helper::setFormInput(['label' => 'Наименование',
										'control' => 'description',
										'type' => 'text',
										'class' => $data['description_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['description'],
										'success' => $data['description_scs'],
										'error' => $data['description_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(DICT_UNIVERSITY['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
