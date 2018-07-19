<?php

namespace backend\models;

use tinyframe\core\Model as Model;
use common\models\Model_User as User;

class Model_User extends Model
{
	/*
		Users processing
	*/

	/**
     * Logins as user.
     *
     * @return boolean
     */
	public function mask() : bool
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = htmlspecialchars($_GET['id']);
		} else {
			echo '<p><strong>Ошибка!</strong> Отсутствует идент-р пользователя!</p>';
			return false;
		}
		$user = new User();
		$user->id = $id;
		$user_row = $user->get();
		if ($user_row) {
			$user->username = $user_row['username'];
			$user->email = $user_row['email'];
			$user->role = $user_row['role'];
			$user->status = $user_row['status'];
			$user->setUser();
			return true;
		} else {
			echo '<p><strong>Ошибка!</strong> Пользователь с ID '.$id.' не найден!</p>';
			return false;
		}
	}
}
