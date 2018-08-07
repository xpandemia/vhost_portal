<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_DictionaryManager as DictionaryManager;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления справочника!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(DICT_MANAGER['ctr'], DICT_MANAGER['act'], DICT_MANAGER['id'], DICT_MANAGER['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
	</div>
	<?php
		// dict_code
		echo Form_Helper::setFormInput(['label' => 'Код',
										'control' => 'dict_code',
										'type' => 'text',
										'class' => $data['dict_code_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['dict_code'],
										'success' => $data['dict_code_scs'],
										'error' => $data['dict_code_err']]);
		// dict_name
		echo Form_Helper::setFormInput(['label' => 'Наименование',
										'control' => 'dict_name',
										'type' => 'text',
										'class' => $data['dict_name_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['dict_name'],
										'success' => $data['dict_name_scs'],
										'error' => $data['dict_name_err']]);
		// dict_filter
		echo Form_Helper::setFormInput(['label' => 'Фильтр',
										'control' => 'dict_filter',
										'type' => 'text',
										'class' => $data['dict_filter_cls'],
										'required' => 'no',
										'value' => $data['dict_filter'],
										'success' => $data['dict_filter_scs'],
										'error' => $data['dict_filter_err']]);
		// type
		echo Form_Helper::setFormSelectList(['label' => 'Тип',
											'control' => 'type',
											'class' => $data['type_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'source' => DictionaryManager::ROLE_LIST,
											'value' => $data['type'],
											'success' => $data['type_scs'],
											'error' => $data['type_err']]);
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
		// model_class
		echo Form_Helper::setFormInput(['label' => 'Наименование модели',
										'control' => 'model_class',
										'type' => 'text',
										'class' => $data['model_class_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'value' => $data['model_class'],
										'success' => $data['model_class_scs'],
										'error' => $data['model_class_err']]);
		// clear_load
		echo Form_Helper::setFormInput(['label' => 'Наименование метода очистки',
										'control' => 'clear_load',
										'type' => 'text',
										'class' => $data['clear_load_cls'],
										'required' => 'no',
										'value' => $data['clear_load'],
										'success' => $data['clear_load_scs'],
										'error' => $data['clear_load_err']]);
		// active
		echo Form_Helper::setFormRadio(['label' => 'Активен',
										'control' => 'active',
										'required' => 'yes',
										'required_style' => 'StarUp',
										'radio' => [
													'no' => ['0' => 'Нет'],
													'yes' => ['1' => 'Да'],
													],
										'value' => $data['active'],
										'error' => $data['active_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(DICT_MANAGER['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
