<?php

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check login
	if (!isset($_SESSION[APP_CODE]['user_name'])) {
		Basic_Helper::redirectHome();
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php echo Form_Helper::setFormBegin(DICT_MANAGER['ctr'], DICT_MANAGER['act'], DICT_MANAGER['id'], DICT_MANAGER['hdr']); ?>

		<!-- dictionary -->
		<?php echo Form_Helper::setFormSelectListDB(['label' => 'Справочник',
													'control' => 'dictionary',
													'class' => $data['dictionary_cls'],
													'required' => 'yes',
													'required_style' => 'StarUp',
													'model_class' => 'common\\models\\Model_DictionaryManager',
													'model_method' => 'getAll',
													'model_field' => 'id',
													'model_field_name' => 'dict_name',
													'value' => $data['dictionary'],
													'success' => $data['dictionary_scs'],
													'error' => $data['dictionary_err']]); ?>
		<!-- controls -->
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_renew', 'Обновить');
				?>
			</div>
		</div>

	<?php
		echo Form_Helper::setFormEnd();
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
	?>
</div>