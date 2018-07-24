<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_DictCountries as DictCountries;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления страны!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DICT_COUNTRIES['ctr'], DICT_COUNTRIES['act'], DICT_COUNTRIES['id'], DICT_COUNTRIES['hdr']);
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
		// fullname
		echo Form_Helper::setFormInput(['label' => 'Полное наименование',
										'control' => 'fullname',
										'type' => 'text',
										'class' => $data['fullname_cls'],
										'required' => 'no',
										'value' => $data['fullname'],
										'success' => $data['fullname_scs'],
										'error' => $data['fullname_err']]);
		// abroad
		echo Form_Helper::setFormSelectList(['label' => 'Зарубежье',
											'control' => 'abroad',
											'class' => $data['abroad_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'source' => DictCountries::ABROAD_LIST,
											'value' => $data['abroad'],
											'success' => $data['abroad_scs'],
											'error' => $data['abroad_err']]);
		// code_alpha2
		echo Form_Helper::setFormInput(['label' => 'Альфа-2',
										'control' => 'code_alpha2',
										'type' => 'text',
										'class' => $data['code_alpha2_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['code_alpha2'],
										'success' => $data['code_alpha2_scs'],
										'error' => $data['code_alpha2_err']]);
		// code_alpha3
		echo Form_Helper::setFormInput(['label' => 'Альфа-3',
										'control' => 'code_alpha3',
										'type' => 'text',
										'class' => $data['code_alpha3_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['code_alpha3'],
										'success' => $data['code_alpha3_scs'],
										'error' => $data['code_alpha3_err']]);
		// guid
		if (!isset($data['id']) || empty($data['id'])) {
			echo Form_Helper::setFormInput(['label' => 'Глобальный идентификатор',
											'control' => 'guid',
											'type' => 'text',
											'class' => $data['guid_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'value' => $data['guid'],
											'success' => $data['guid_scs'],
											'error' => $data['guid_err']]);
		}
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(DICT_COUNTRIES['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
