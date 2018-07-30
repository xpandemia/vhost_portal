<?php

use tinyframe\core\helpers\HTML_Helper as HTML_Helper;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use common\models\Model_User as User;

	// check data
	if (!isset($data)) {
		Basic_Helper::redirect(APP_NAME, 204, 'Main', 'Index', null, 'Ошибка добавления пользователя!');
	}
?>
<div class="container rounded bg-light pl-5 pr-5 pt-3 pb-3 mt-5">
	<?php
		echo HTML_Helper::setAlert($data['success_msg'], 'alert-success');
		echo HTML_Helper::setAlert($data['error_msg'], 'alert-danger');
		echo Form_Helper::setFormBegin(USER['ctr'], USER['act'], USER['id'], USER['hdr']);
	?>
	<div class="form-group">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($data['id'])) ? $data['id'] : null; ?>"/>
	</div>
	<?php
		// username
		echo Form_Helper::setFormInput(['label' => 'Логин',
										'control' => 'username',
										'type' => 'text',
										'class' => $data['username_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => USERNAME_PLC,
										'value' => $data['username'],
										'success' => $data['username_scs'],
										'error' => $data['username_err'],
										'help' => USERNAME_HELP]);
		// email
		echo Form_Helper::setFormInput(['label' => 'Адрес эл. почты',
										'control' => 'email',
										'type' => 'email',
										'class' => $data['email_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => EMAIL_PLC,
										'value' => $data['email'],
										'success' => $data['email_scs'],
										'error' => $data['email_err'],
										'help' => EMAIL_HELP]);
		// pwd
		echo Form_Helper::setFormInput(['label' => 'Пароль',
										'control' => 'pwd',
										'type' => 'password',
										'class' => $data['pwd_cls'],
										'required' => 'yes',
										'required_style' => 'StarUp',
										'placeholder' => PWD_PLC,
										'value' => $data['pwd'],
										'success' => $data['pwd_scs'],
										'error' => $data['pwd_err'],
										'help' => PWD_HELP]);
		// role
		echo Form_Helper::setFormSelectList(['label' => 'Роль',
											'control' => 'role',
											'class' => $data['role_cls'],
											'required' => 'yes',
											'required_style' => 'StarUp',
											'source' => User::ROLE_LIST,
											'value' => $data['role'],
											'success' => $data['role_scs'],
											'error' => $data['role_err']]);
	?>
	<!-- controls -->
	<p></p>
	<div class="form-group">
		<div class="col">
			<?php
				echo HTML_Helper::setSubmit('btn btn-success', 'btn_save', 'Сохранить');
				echo HTML_Helper::setHrefButton(USER['ctr'], 'Index', 'btn btn-warning', 'Отмена');
			?>
		</div>
	</div>
	<?php
		echo Form_Helper::setFormEnd();
	?>
</div>
