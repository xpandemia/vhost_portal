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
     * @return array
     */
	public function getForm($rules, $post)
	{
		if (is_array($rules) && is_array($post)) {
			foreach ($rules as $field_name => $rule_name_arr) {
				switch ($rules[$field_name]['type']) {
					case 'checkbox':
						if (isset($post[$field_name])) {
							$form[$field_name] = 'checked';
						} else {
							$form[$field_name] = null;
						}
						break;
					default:
						if (isset($post[$field_name])) {
							$form[$field_name] = htmlspecialchars($post[$field_name]);
						} else {
							$form[$field_name] = null;
						}
				}
				$form[$field_name.'_cls'] = $rules[$field_name]['class'];
				$form[$field_name.'_scs'] = $rules[$field_name]['success'];
				$form[$field_name.'_err'] = null;
				$form[$field_name.'_vis'] = true;
			}
			return $form;
		} else {
			throw new \InvalidArgumentException('На входе функции Model.getForm могут быть только массивы!');
		}
	}

	/**
     * Validates form data.
     *
     * @return boolean
     */
	public function validateForm($form, $rules)
	{
		if (is_array($form) && is_array($rules)) {
			$form = $this->resetForm(false, $form, $rules);
			$form_helper = new Form_Helper();
			return $form_helper->validate($form, $rules);
		} else {
			throw new \InvalidArgumentException('На входе функции Model.validateForm могут быть только массивы!');
		}
	}

	/**
     * Sets form data.
     *
     * @return array
     */
	public function setForm($rules, $row)
	{
		if (is_array($rules)) {
			foreach ($rules as $field_name => $rule_name_arr) {
				if ($row && isset($row[$field_name])) {
					if ($rules[$field_name]['type'] === 'date') {
						$form[$field_name] = date($rules[$field_name]['format'], strtotime($row[$field_name]));
					} else {
						$form[$field_name] = $row[$field_name];
					}
				} else {
					$form[$field_name] = null;
				}
				$form[$field_name.'_cls'] = $rules[$field_name]['class'];
				$form[$field_name.'_scs'] = $rules[$field_name]['success'];
				$form[$field_name.'_err'] = null;
				$form[$field_name.'_vis'] = true;
			}
			$form['success_msg'] = null;
			$form['error_msg'] = null;
			return $form;
		} else {
			throw new \InvalidArgumentException('На входе функции Model.setForm должен быть массив правил!');
		}
	}

	/**
     * Resets form data.
     *
     * @return array
     */
	public function resetForm($vars, $form, $rules)
	{
		if (is_array($rules)) {
			foreach ($rules as $field_name => $rule_name_arr) {
				if ($vars === true) {
					$form[$field_name] = null;
				}
				$form[$field_name.'_cls'] = $rules[$field_name]['class'];
				$form[$field_name.'_scs'] = $rules[$field_name]['success'];
				$form[$field_name.'_err'] = null;
			}
			$form['success_msg'] = null;
			$form['error_msg'] = null;
			return $form;
		} else {
			throw new \InvalidArgumentException('На входе функции Model.resetForm должен быть массив правил!');
		}
	}
}
