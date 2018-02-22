<?php

namespace tinyframe\core;

use tinyframe\core\helpers\Form_Helper as Form_Helper;

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
     * Gets form data.
     *
     * @return nothing
     */
	public function getForm($rules, $post)
	{
		if (is_array($rules) && is_array($post)) {
			foreach ($rules as $field_name => $rule_name_arr) {
				switch ($rules[$field_name]['type']) {
					case 'checkbox':
						if (isset($post[$field_name])) {
							$_SESSION[$this->form][$field_name] = 'checked';
						} else {
							$_SESSION[$this->form][$field_name] = null;
						}
						break;
					default:
						$_SESSION[$this->form][$field_name] = htmlspecialchars($post[$field_name]);
				}
			}
		}
		else {
			throw new InvalidArgumentException('На входе функции Model.getForm могут быть только массивы!');
		}
	}

	/**
     * Validates form data.
     *
     * @return boolean
     */
	public function validateForm($form, $rules)
	{
		$this->resetForm(false, $form, $rules);
		$form_helper = new Form_Helper();
		return $form_helper->validate($form, $_SESSION[$form], $rules);
	}

	/**
     * Sets form data.
     *
     * @return nothing
     */
	public function setForm($form, $rules, $row)
	{
		foreach ($rules as $field_name => $rule_name_arr) {
			if ($row) {
				if ($rules[$field_name]['type'] === 'date') {
					$_SESSION[$form][$field_name] = date($rules[$field_name]['format'], strtotime($row[$field_name]));
				} else {
					$_SESSION[$form][$field_name] = $row[$field_name];
				}
			} else {
				$_SESSION[$form][$field_name] = null;
			}
			$_SESSION[$form][$field_name.'_cls'] = $rules[$field_name]['class'];
			$_SESSION[$form][$field_name.'_err'] = null;
			$_SESSION[$form][$field_name.'_vis'] = true;
			$_SESSION[$form]['success_msg'] = null;
			$_SESSION[$form]['error_msg'] = null;
		}
	}

	/**
     * Resets form data.
     *
     * @return nothing
     */
	public function resetForm($vars, $form, $rules)
	{
		foreach ($rules as $field_name => $rule_name_arr) {
			if ($vars === true) {
				$_SESSION[$form][$field_name] = null;
			}
			$_SESSION[$form][$field_name.'_cls'] = $rules[$field_name]['class'];
			$_SESSION[$form][$field_name.'_err'] = null;
		}
		$_SESSION[$form]['success_msg'] = null;
		$_SESSION[$form]['error_msg'] = null;
	}
}
