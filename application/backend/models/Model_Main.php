<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use common\models\Model_User as Model_User;

class Model_Main extends Model
{
	/*
		Main processing
	*/

	/**
     * Checks user.
     *
     * @return void
     */
	public function checkUser($user_name, $user_id)
	{
		if (isset($user_name)) {
			$user = new Model_User();
			$user->username = $user_name;
			$user_row = $user->getByUsername();
			if ($user_row) {
				if ($user_row['role'] == $user::ROLE_ADMIN) {
					$user->id = $user_row['id'];
					$user->email = $user_row['email'];
					$user->role = $user_row['role'];
					$user->status = $user_row['status'];
					$user->setUser();
				} else {
					exit("<p><strong>Ошибка!</strong> Доступ только для администраторов.</p>");
				}
			} else {
				exit("<p><strong>Ошибка!</strong> Регистрация не выполнена.</p>");
			}
		} else {
			return Basic_Helper::redirectHome();
		}
	}

	/**
     * Logs user out.
     *
     * @return void
     */
	public function logout()
	{
		$user = new Model_User();
		$user->unsetUser();
		ob_end_clean(); // discard output buffer
		session_destroy();
		switch (LOGON) {
			case 'login':
				session_start();
				ob_start(); // start output buffer
				Basic_Helper::redirect(LOGIN['hdr'], 202, LOGIN['ctr'], 'Index');
			case 'cas':
				\phpCAS::logout();
			default:
				session_start();
				ob_start(); // start output buffer
				Basic_Helper::redirect(LOGIN['hdr'], 202, LOGIN['ctr'], 'Index');
		}
	}
}
