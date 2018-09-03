<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_Langs as Langs;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления языка!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(LANGS['ctr'], LANGS['act'], LANGS['id'], LANGS['hdr']);
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
		// name_original
		echo Form_Helper::setFormInput(['label' => 'Наименование на родном языке',
										'control' => 'name_original',
										'type' => 'text',
										'class' => $data['name_original_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['name_original'],
										'success' => $data['name_original_scs'],
										'error' => $data['name_original_err']]);
		// name_eng
		echo Form_Helper::setFormInput(['label' => 'Наименование на английском языке',
										'control' => 'name_eng',
										'type' => 'text',
										'class' => $data['name_eng_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['name_eng'],
										'success' => $data['name_eng_scs'],
										'error' => $data['name_eng_err']]);
		// name_rus
		echo Form_Helper::setFormInput(['label' => 'Наименование на русском языке',
										'control' => 'name_rus',
										'type' => 'text',
										'class' => $data['name_rus_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['name_rus'],
										'success' => $data['name_rus_scs'],
										'error' => $data['name_rus_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(LANGS['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
