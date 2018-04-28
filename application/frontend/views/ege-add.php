<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(EGE['ctr'], EGE['act'], EGE['id'], EGE['hdr']);
			// description
			echo Form_Helper::setFormInput(['label' => 'Описание',
											'control' => 'description',
											'type' => 'text',
											'class' => $data['description_cls'],
											'required' => 'no',
											'value' => $data['description'],
											'success' => $data['description_scs'],
											'error' => $data['description_err']]);			
			// reg_year
			echo Form_Helper::setFormInput(['label' => 'Год сдачи',
											'control' => 'reg_year',
											'type' => 'text',
											'class' => $data['reg_year_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'value' => $data['reg_year'],
											'success' => $data['reg_year_scs'],
											'error' => $data['reg_year_err']]);
	?>
	<!-- controls -->
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(EGE['ctr'], 'Reset', 'btn btn-danger', 'Очистить');
				echo HTML_Helper::setHrefButton(EGE['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>

<script>
	$(function(){
	  $("#reg_year").mask("9999");
	});
</script>
