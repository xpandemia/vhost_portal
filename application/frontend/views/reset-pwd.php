<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php echo Form_Helper::setFormBegin(RESET_PWD['ctr'], RESET_PWD['act'], RESET_PWD['id'], RESET_PWD['hdr']); ?>

		<!-- pwd -->
		<?php echo Form_Helper::setFormInput(['label' => '<i class="fas fa-keyboard fa-2x"></i>',
											'control' => 'pwd',
											'type' => 'password',
											'class' => $data['pwd_cls'],
											'required' => 'yes',
											'required_style' => 'RedBold',
											'placeholder' => PWD_PLC,
											'value' => $data['pwd'],
											'success' => $data['pwd_scs'],
											'error' => $data['pwd_err'],
											'help' => PWD_HELP]); ?>
		<!-- pwd_confirm -->
		<?php echo Form_Helper::setFormInput(['label' => '<i class="fas fa-keyboard fa-2x"></i>',
											'control' => 'pwd_confirm',
											'type' => 'password',
											'class' => $data['pwd_confirm_cls'],
											'required' => 'yes',
											'required_style' => 'RedBold',
											'placeholder' => PWD_CONFIRM_PLC,
											'value' => $data['pwd_confirm'],
											'success' => $data['pwd_confirm_scs'],
											'error' => $data['pwd_confirm_err'],
											'help' => PWD_CONFIRM_HELP]); ?>
		<!-- controls -->
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_reset_pwd', 'Сменить');
					echo HTML_Helper::setHrefButton(RESET_PWD['ctr'], 'Reset', 'btn btn-danger', 'Очистить');
					echo HTML_Helper::setHrefButton(LOGIN['ctr'], 'Index', 'btn btn-primary', 'Войти');
				?>
			</div>
		</div>

	<?php
		echo Form_Helper::setFormEnd();
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
	?>
</div>
