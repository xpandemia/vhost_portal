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

	public $form = 'signup';

	/**
     * Signup rules.
     *
     * @return array
     */
	public function rules()
	{
		return [
                'username' => [
                                'required' => ['value' => 'true', 'default' => '', 'msg' => 'Логин обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_ALPHA, 'msg' => 'Для логина можно использовать только буквы!'],
                                'width' => ['value' => 'true', 'format' => 'string', 'min' => 1, 'max' => 45, 'msg' => 'Слишком длинный логин!'],
                                'unique' => ['value' => 'true', 'class' => 'common\\models\\Model_User', 'method' => 'ExistsUsername', 'msg' => 'Такой логин уже есть!']
                               ],
                'email' => [
                                'required' => ['value' => 'true', 'default' => '', 'msg' => 'Адрес эл. почты обязателен для заполнения!'],
                                'pattern' => ['value' => PATTERN_EMAIL_LIGHT, 'msg' => 'Адрес электронной почты должен быть в формате user@domain.ru'],
                                'width' => ['value' => 'true', 'format' => 'string', 'min' => 0, 'max' => 45, 'msg' => 'Слишком длинный адрес эл. почты!'],
                                'unique' => ['value' => 'true', 'class' => 'common\\models\\Model_User', 'method' => 'ExistsEmail', 'msg' => 'Такой адрес эл. почты уже есть!']
                               ],
                'pwd' => [
                            'required' => ['value' => 'true', 'default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['value' => 'true', 'format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!']
                           ],
				'pwd_confirm' => [
                            'required' => ['value' => 'true', 'default' => '', 'msg' => 'Пароль обязателен для заполнения!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Пароль должен быть буквенно-цифровым!'],
                            'width' => ['value' => 'true', 'format' => 'string', 'min' => 6, 'max' => 10, 'msg' => 'Пароль должен быть 6-10 символов длиной!']
                           ],
                'captcha' => [
                            'required' => ['value' => 'true', 'default' => '', 'msg' => 'Указанный на изображении код обязателен!'],
                            'pattern' => ['value' => PATTERN_ALPHA_NUMB, 'msg' => 'Код, указанный на изображении, должен быть буквенно-цифровым!'],
                            'width' => ['value' => 'true', 'format' => 'string', 'min' => CAPTCHA_LEN, 'max' => CAPTCHA_LEN, 'msg' => 'Код, указанный на изображении, должен быть '.CAPTCHA_LEN.' символов длиной!'],
                            'compared' => ['type' => '==', 'value' => $_SESSION['captcha'], 'msg' => 'Введен неверный код, указанный на изображении!']
                           ]
            ];
	}

	/**
     * Signup reset.
     *
     * @return nothing
     */
	public function reset($vars)
	{
		$this->resetForm($vars, $this->form, $this->rules());
	}

	/**
     * Gets signup page data.
     *
     * @return nothing
     */
	public function getPost($post)
	{
		foreach ($post as $varname => $varvalue) {
			$_SESSION[$this->form][$varname] = htmlspecialchars($varvalue);
		}
	}

	/**
     * Validates signup page.
     *
     * @return boolean
     */
	public function validate()
	{
		$this->reset(false);
		$form_helper = new Form_Helper();
		return $form_helper->validate($this->form, $_SESSION[$this->form], $this->rules());
	}

	/**
     * Saves user data.
     *
     * @return boolean
     */
	public function signup()
	{
		if ($_SESSION[$this->form]['pwd'] == $_SESSION[$this->form]['pwd_confirm']) {
			// user register
			$user = new Model_User();
			$user->username = $_SESSION[$this->form]['username'];
			$user->email = $_SESSION[$this->form]['email'];
			$user->pwd_hash = $user->GetHash($_SESSION[$this->form]['pwd']);
			$user->activation = $user->GetHash($_SESSION[$this->form]['email'].date('Y-m-d H:i:s'));
			$user->status = 0;
			$user->dt_created = date('Y-m-d H:i:s');
			if ($user->save()) {
				// send activation email
				$mail = new Mail_Helper;
				$subject = 'Регистрация в '.APP_NAME;
				$message = '
							<html>
							    <head>
							        <title>Регистрация в '.APP_NAME.'</title>
							    </head>
							    <body>
									<h1>'.$_SESSION[$this->form]['username'].', здравствуйте!</h1>
							        <p>Вы зарегистрировались на сайте '.APP_NAME.'. Для активации вашей учетной записи необходимо пройти по ссылке <a href="'.BASEPATH.'/'.BEHAVIOR.'/Signup/Activation/?code='.$user->activation.'&email='.$user->email.'">подтверждения регистрации</a></p>
							        <p>В случае, если это письмо пришло Вам ошибочно, просто игнорируйте его.</p>
							        <p>С уважением, '.APP_NAME.'.</p>
							    </body>
							</html>';
				if ($mail->sendEmail($_SESSION[$this->form]['email'], $_SESSION[$this->form]['username'], $subject, $message)) {
					return TRUE;
				} else {
					$_SESSION[$this->form]['error_msg'] = 'Ошибка при отправке эл. сообщения!';
					return FALSE;
				}
			} else {
				$_SESSION[$this->form]['error_msg'] = 'Ошибка при сохранении пользователя!';
				return FALSE;
			}
		} else {
			$_SESSION[$this->form]['error_msg'] = 'Пароли не совпадают!';
			return FALSE;
		}
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
		$row = $user->getUserByEmail();
		if ($activation == $row['activation']) {
			$_SESSION[$this->form]['username'] = $row['username'];
			$user->status = $user::STATUS_ACTIVE;
			return $user->changeStatus();
		} else {
			return FALSE;
		}
	}
}
