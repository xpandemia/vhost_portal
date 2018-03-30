<?php
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php echo Form_Helper::setFormBegin(RESET_PWD_REQUEST['ctr'], RESET_PWD_REQUEST['act'], RESET_PWD_REQUEST['id'], RESET_PWD_REQUEST['hdr']); ?>

		<!-- email -->
		<?php echo Form_Helper::setFormInput(['label' => '<i class="fas fa-envelope fa-2x"></i>',
											'control' => 'email',
											'type' => 'email',
											'class' => $data['email_cls'],
											'required' => 'yes',
											'required_style' => 'RedBold',
											'placeholder' => EMAIL_PLC,
											'value' => $data['email'],
											'success' => $data['email_scs'],
											'error' => $data['email_err'],
											'help' => EMAIL_HELP]); ?>
		<!-- controls -->
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_send_email', 'Выслать письмо');
					echo HTML_Helper::setHrefButton(RESET_PWD_REQUEST['ctr'], 'Reset', 'btn btn-danger', 'Очистить');
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
