<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления связи уровней подготовки с типами документов!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(EDUCLEVELS_DOCTYPES['ctr'], EDUCLEVELS_DOCTYPES['act'], EDUCLEVELS_DOCTYPES['id'], EDUCLEVELS_DOCTYPES['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
	</div>
	<?php
		// educ level
		echo Form_Helper::setFormSelectListDB(['label' => 'Уровень подготовки',
												'control' => 'educ_level',
												'class' => $data['educ_level_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictEduclevels',
												'model_method' => 'getAll',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['educ_level'],
												'success' => $data['educ_level_scs'],
												'error' => $data['educ_level_err']]);
		// doc type
		echo Form_Helper::setFormSelectListDB(['label' => 'Тип документа',
												'control' => 'doc_type',
												'class' => $data['doc_type_cls'],
												'required' => 'yes',
												'required_style' => 'StarUp',
												'model_class' => 'common\\models\\Model_DictDoctypes',
												'model_method' => 'getDiplomas',
												'model_field' => 'code',
												'model_field_name' => 'description',
												'value' => $data['doc_type'],
												'success' => $data['doc_type_scs'],
												'error' => $data['doc_type_err']]);
		// pay
		echo Form_Helper::setFormCheckbox(['label' => 'Платное образование',
											'control' => 'pay',
											'class' => $data['pay_cls'],
											'value' => $data['pay'],
											'success' => $data['pay_scs'],
											'error' => $data['pay_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(EDUCLEVELS_DOCTYPES['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
