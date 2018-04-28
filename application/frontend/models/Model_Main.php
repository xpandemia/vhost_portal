<?php

namespace frontend\models;

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
     * @return boolean
     */
	public function checkUser()
	{
		$user = new Model_User();
		$user->username = $_SESSION[APP_CODE]['user_name'];
		$user_row = $user->getByUsername();
		if ($user_row) {
			$user->id = $user_row['id'];
			$user->email = $user_row['email'];
			$user->role = $user_row['role'];
			$user->status = $user_row['status'];
			$user->setUser();
			return true;
		} else {
			$user->email = $_SESSION[APP_CODE]['user_name'].'@bsu.edu.ru';
			if ($user->save()) {
				$user_row = $user->getByUsername();
				if ($user_row) {
					$user->id = $user_row['id'];
					$user->email = $user_row['email'];
					$user->role = $user_row['role'];
					$user->status = $user_row['status'];
					$user->setUser();
					return true;
				}
			} else {
				return false;
			}
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
			case 'case':
				\phpCAS::logout();
			default:
				session_start();
				ob_start(); // start output buffer
				Basic_Helper::redirect(LOGIN['hdr'], 202, LOGIN['ctr'], 'Index');
		}
	}
}
