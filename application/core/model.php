<?php

namespace tinyframe\core;

class Model
{
	/*
		BASE Model

		Models usually include select data methods:
			> methods of native libraries methods PGSQL or MYSQL;
			> methods of data astraction libraries. PEAR MDB2 for example;
			> ORM methods;
			> methods for NoSQL;
			> and others.
	*/

	/**
     * Form reset.
     *
     * @return nothing
     */
	public function resetForm($vars, $form, $rules)
	{
		foreach ($rules as $field_name => $rule_name_arr) {
			if ($vars === true) {
				$_SESSION[$form][$field_name] = null;
			}
			$_SESSION[$form][$field_name.'_cls'] = 'form-control';
			$_SESSION[$form][$field_name.'_err'] = null;
		}
		$_SESSION[$form]['success_msg'] = null;
		$_SESSION[$form]['error_msg'] = null;
	}
}
