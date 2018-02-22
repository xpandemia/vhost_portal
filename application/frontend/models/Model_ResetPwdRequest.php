<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\Mail_Helper as Mail_Helper;
use common\models\Model_User as Model_User;

class Model_ResetPwdRequest extends Model
{
	/*
		Reset password request processing
	*/

	public $form = 'reset_pwd_request';

	/**
     * Reset password request rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'email' => [
							'type' => 'email',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Адрес эл. почты обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_EMAIL_LIGHT, 'msg' => 'Адрес электронной почты должен быть в формате user@domain.ru'],
                            'width' => ['format' => 'string', 'min' => 0, 'max' => 45, 'msg' => 'Слишком длинный адрес эл. почты!']
                            ]
            ];
	}

	/**
     * Sets password token and sends confirmation email.
     *
     * @return boolean
     */
	public function sendEmail()
	{
		$user = new Model_User();
		$user->email = $_SESSION[$this->form]['email'];
		$user->pwd_token = $user->GetHash($_SESSION[$this->form]['email'].date('Y-m-d H:i:s'));
		$row = $user->getUserByEmail();
		if (!empty($row)) {
			if ($user->changePwdToken()) {
				$mail = new Mail_Helper;
				$subject = 'Восстановление пароля в '.APP_NAME;
				$message = '
							<html>
							    <head>
							        <title>Регистрация в '.APP_NAME.'</title>
							    </head>
							    <body>
									<h1>'.$row['username'].', здравствуйте!</h1>
							        <p>Вы отправили запрос на восстановление пароля. Для того, чтобы задать новый пароль, перейдите по ссылке <a href="'.BASEPATH.'/'.BEHAVIOR.'/ResetPwd/CheckResetPwd/?pwd_token='.$user->pwd_token.'&email='.$user->email.'">для изменения пароля</a></p>
							        <p>Если вы не делали запроса для получения пароля, то просто удалите данное письмо, ваш пароль хранится в надежном месте и недоступен посторонним лицам.</p>
							        <p>С уважением, '.APP_NAME.'.</p>
							    </body>
							</html>';
				if ($mail->sendEmail($row['email'], $row['username'], $subject, $message)) {
					return TRUE;
				} else {
					$_SESSION[$this->form]['error_msg'] = 'Ошибка при отправке эл. сообщения!';
					return FALSE;
				}
			} else {
				$_SESSION[$this->form]['error_msg'] = 'Ошибка установки признака изменения пароля!';
				return FALSE;
			}
		} else {
			$_SESSION[$this->form]['error_msg'] = 'Адрес эл. почты не найден!';
			return FALSE;
		}
	}
}
