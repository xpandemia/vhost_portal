<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, nl2br("Ошибка добавления дисциплины ЕГЭ!\nСвяжитесь с администратором."));
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(EGE_DSP['ctr'], EGE_DSP['act'], EGE_DSP['id'], EGE_DSP['hdr']);
			// discipline
			echo Form_Helper::setFormSelectListDB(['label' => 'Дисциплина',
													'control' => 'discipline',
													'class' => $data['discipline_cls'],
													'required' => 'yes',
													'required_style' => 'StarUp',
													'model_class' => 'common\\models\\Model_DictEge',
													'model_method' => 'getAll',
													'model_field' => 'code',
													'model_field_name' => 'description',
													'value' => $data['discipline'],
													'success' => $data['discipline_scs'],
													'error' => $data['discipline_err']]);
			// points
			echo Form_Helper::setFormInput(['label' => 'Баллы',
											'control' => 'points',
											'type' => 'text',
											'class' => $data['points_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'value' => $data['points'],
											'success' => $data['points_scs'],
											'error' => $data['points_err']]);
	?>
	<div class="form-group">
		<input type="hidden" id="pid" name="pid" value="<?php echo $data['pid']; ?>"/>
	</div>
	<!-- controls -->
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(EGE_DSP['ctr'], 'Reset/?pid='.$data['pid'], 'btn btn-danger', 'Очистить');
				echo HTML_Helper::setHrefButton(EGE_DSP['ctr'], 'Index/?pid='.$data['pid'], 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
