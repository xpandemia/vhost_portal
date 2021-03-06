<?php

namespace frontend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Form_Helper as Form_Helper;
use tinyframe\core\helpers\Mail_Helper as Mail_Helper;
use common\models\Model_User as Model_User;

class Model_Signup extends Model
{
	/*
		Signup processing
	*/

	/**
     * Signup rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'username' => [
                                'type' => 'text',
                                'class' => 'form-control',
                                'required' => ['default' => '', 'msg' => 'Логин обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для логина можно использовать '.MSG_ALPHA.'!'],
                                'width' => ['format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный логин!'],
                                'unique' => ['class' => 'common\\models\\Model_User', 'method' => 'ExistsUsername', 'msg' => 'Такой логин уже есть!'],
                                'success' => 'Логин заполнен верно.'
                               ],
                'email' => [
                            'type' => 'email',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Адрес эл. почты обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_EMAIL_LIGHT, 'msg' => 'Адрес электронной почты должен быть '.MSG_EMAIL_LIGHT],
                            'width' => ['format' => 'string', 'min' => 0, 'max' => 45, 'msg' => 'Слишком длинный адрес эл. почты!'],
                            'unique' => ['class' => 'common\\models\\Model_User', 'method' => 'ExistsEmail', 'msg' => 'Такой адрес эл. почты уже есть!'],
                            'success' => 'Адрес эл. почты заполнен верно.'
                           ],
                'pwd' => [
							'type' => 'password',
                            'class' => 'form-control',
	                        'required' => ['default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
	                        'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Для пароля можно использовать '.MSG_ALPHA_NUMB.'!'],
	                        'width' => ['format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!'],
	                        'success' => 'Пароль заполнен верно.'
	                       ],
				'pwd_confirm' => [
                            'type' => 'password',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Для пароля можно использовать '.MSG_ALPHA_NUMB.'!'],
                            'width' => ['format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!'],
                            'success' => 'Пароль заполнен верно.'
                           ],
                'captcha' => [
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => ['default' => '', 'msg' => 'Указанный на изображении код обязателен!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Для кода, указанного на изображении, можно использовать '.MSG_ALPHA_NUMB.'!'],
                            'width' => ['format' => 'string', 'min' => CAPTCHA_LEN, 'max' => CAPTCHA_LEN, 'msg' => 'Код, указанный на изображении, должен быть '.CAPTCHA_LEN.' символов длиной!'],
                            'compared' => ['type' => '==', 'value' => $_SESSION[APP_CODE]['captcha'], 'msg' => 'Введен неверный код, указанный на изображении!'],
                            'success' => 'Код, указанный на изображении, введён верно, но из-за других ошибок, пожалуйста, введите его ещё раз.'
                           ]
            ];
	}

	/**
     * Saves user data.
     *
     * @return array
     */
	public function signup($form)
	{
		if ($form['pwd'] == $form['pwd_confirm']) {
			$user = new Model_User();
			$user->username = $form['username'];
			$user->email = $form['email'];
			$user->pwd_hash = $user->GetHash($form['pwd']);
			$user->role = $user::ROLE_GUEST;
			switch (SIGNUP) {
				case 'login':
					$user->status = $user::STATUS_ACTIVE;
					if ($user->save() > 0) {
						$form['success_msg'] = 'Регистрация выполнена успешно.';
						$form['error_msg'] = null;
					} else {
						$form['error_msg'] = 'Ошибка при сохранении пользователя!';
					}
				case 'email':
					$user->activation = $user->GetHash($form['email'].date('Y-m-d H:i:s'));
					$user->status = $user::STATUS_NOTACTIVE;
					if ($user->save() > 0) {
						// send activation email
						$mail = new Mail_Helper();
						$subject = 'Регистрация в '.APP_NAME;
						$message = '
									<html>
									    <head>
									        <title>Регистрация в '.APP_NAME.'</title>
									    </head>
									    <body>
											<h1>'.$form['username'].', здравствуйте!</h1>
									        <p>Вы зарегистрировались на сайте '.APP_NAME.'. Для активации вашей учетной записи необходимо пройти по ссылке <a href="'.BASEPATH.'/'.BEHAVIOR.'/Signup/Activation/?code='.$user->activation.'&email='.$user->email.'">подтверждения регистрации</a></p>
									        <p>В случае, если это письмо пришло Вам ошибочно, просто игнорируйте его.</p>
									        <p>С уважением, '.APP_NAME.'.</p>
									    </body>
									</html>';
						if ($mail->sendEmail($form['email'], $form['username'], $subject, $message)) {
							$form['success_msg'] = 'Регистрация выполнена успешно. Пожалуйста, проверьте электронную почту.';
							$form['error_msg'] = null;
						} else {
							$form['error_msg'] = 'Ошибка при отправке эл. сообщения!';
						}
					} else {
						$form['error_msg'] = 'Ошибка при сохранении пользователя!';
					}
				default:
					$form['error_msg'] = 'Неизвестный тип регистрации!';
			}
		} else {
			$form['error_msg'] = 'Пароли не совпадают!';
		}
		return $form;
	}

	/**
     * Activates user account.
     *
     * @return boolean
     */
	public function activate($activation, $email)
	{
		$user = new Model_User();
		$user->email = $email;
		$row = $user->getByEmail();
		if ($activation == $row['activation']) {
			$user->id = $row['id'];
			$user->status = $user::STATUS_ACTIVE;
			if ($user->changeStatus()) {
				$user->username = $row['username'];
				$user->role = $row['role'];
				$user->setUser();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
