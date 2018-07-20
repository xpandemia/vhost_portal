<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления дисциплины ЕГЭ!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DICT_EGE['ctr'], DICT_EGE['act'], DICT_EGE['id'], DICT_EGE['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
	</div>
	<?php
		// discipline
		echo Form_Helper::setFormSelectListDB(['label' => 'Дисциплина',
												'control' => 'discipline',
												'class' => $data['discipline_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictDiscipline',
												'model_method' => 'getUnique',
												'model_field' => 'code',
												'model_field_name' => 'discipline_name',
												'value' => $data['discipline'],
												'success' => $data['discipline_scs'],
												'error' => $data['discipline_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(DICT_EGE['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
