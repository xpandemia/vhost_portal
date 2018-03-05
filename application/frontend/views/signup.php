<?php
	
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;

	// check login
	if (isset($_SESSION[APP_CODE]['user_id'])) {
		Basic_Helper::redirect(APP_NAME, 202, BEHAVIOR.'/Main', 'Index');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php echo Form_Helper::setFormBegin('Signup/Signup', 'form_signup', SIGNUP_HDR); ?>

		<!-- username -->
		<?php echo Form_Helper::setFormInput(['label' => '<i class="fas fa-user fa-2x"></i>',
											'control' => 'username',
											'type' => 'text',
											'class' => $data['username_cls'],
											'required' => 'yes',
											'required_style' => 'RedBold',
											'placeholder' => USERNAME_PLC,
											'value' => $data['username'],
											'success' => $data['username_scs'],
											'error' => $data['username_err'],
											'help' => USERNAME_HELP]); ?>
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
		<!-- captcha -->
		<hr>
		<?php echo Form_Helper::setFormCaptcha(['action' => 'Signup/Captcha',
												'class' => $data['captcha_cls'],
												'value' => $data['captcha'],
												'success' => $data['captcha_scs'],
												'error' => $data['captcha_err']]); ?>
		<!-- controls -->
		<div class="form-group">
			<div class="col">
				<?php
					echo HTML_Helper::setSubmit('btn btn-success', 'btn_signup', 'Зарегистрироваться');
					echo HTML_Helper::setHrefButton('Signup/Reset', 'btn btn-danger', 'Очистить');
					echo HTML_Helper::setHrefButton('Login/Index', 'btn btn-primary', 'Войти');
				?>
			</div>
		</div>

	<?php
		echo Form_Helper::setFormEnd();
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
	?>
</div>
