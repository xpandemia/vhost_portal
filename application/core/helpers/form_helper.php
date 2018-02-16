<?php

namespace tinyframe\core\helpers;

// patterns
define("PATTERN_ALPHA", "/^[a-zA-Z]*$/"); // letters only
define("PATTERN_ALPHA_NUMB", "/^[a-zA-Z0-9]*$/"); // letters and numbers
define("PATTERN_EMAIL_LIGHT", "/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9_\-.]+$/"); // email light
define("PATTERN_EMAIL_STRONG", "/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+$/"); // email strong

class Form_Helper
{
	/*
		Forms processing
	*/

	/**
     * Validates form.
     *
     * @return boolean
     */

	/* RULES
	'required' => ['value' => 'true/false', 'default' => 'value', 'msg' => 'Required error message.'],
    'pattern' => ['value' => {PATTERN_}, 'msg' => 'Pattern error message.'],
    'width' => ['value' => 'true/false', 'format' => 'string/numb', 'min' => {min value}, 'max' => {max value}, 'msg' => 'Width error message.'],
    'unique' => ['value' => 'true/false', 'class' => 'tinyframe\\models\\common\\Model_Class', 'method' => 'Class_Method', 'msg' => 'Unique error message.'],
    'compared' => ['value' => '{VALUE}', 'type' => '==', 'msg' => 'Compared error message.']
    */
	public function validate($form, $data, $rules)
	{
		$validate = true;
		// RULES loop
		foreach ($rules as $field_name => $rule_name_arr) {
			foreach ($rule_name_arr as $rule_name => $rule_var_arr) {
				// RULE processing
				switch ($rule_name) {
					// required check
					case "required":
						if ($rule_var_arr['value'] == 'true' && empty($data[$field_name])) {
							if (!empty($rule_var_arr['default'])) {
								$data[$field_name] = $rule_var_arr['default'];
								$this->setError($form, $field_name, '');
							} else {
								$validate = false;
								$this->setError($form, $field_name, $rule_var_arr['msg']);
							}
						}
						break;
					// pattern check
					case "pattern":
						if (!empty($rule_var_arr['value']) && !empty($data[$field_name]) && empty($_SESSION['validate_err'][$field_name])) {
							if (!preg_match($rule_var_arr['value'], $data[$field_name])) {
								$validate = false;
								$this->setError($form, $field_name, $rule_var_arr['msg']);
							}
						}
						break;
					// width check
					case "width":
						if ($rule_var_arr['value'] == 'true' && !empty($data[$field_name]) && empty($_SESSION['validate_err'][$field_name])) {
							// format
							switch ($rule_var_arr['format']) {
								// строка
								case "string":
									if (strlen($data[$field_name]) < $rule_var_arr['min'] || strlen($data[$field_name]) > $rule_var_arr['max']) {
										$validate = false;
										$this->setError($form, $field_name, $rule_var_arr['msg']);
									}
									break;
								// число
								case "numb":
									if ($data[$field_name] < $rule_var_arr['min'] || $data[$field_name] > $rule_var_arr['max']) {
										$validate = false;
										$this->setError($form, $field_name, $rule_var_arr['msg']);
									}
									break;
							}
						}
						break;
					// unique check
					case "unique":
						if ($rule_var_arr['value'] == 'true' && !empty($data[$field_name]) && empty($_SESSION['validate_err'][$field_name])) {
							// using model
							$model = new $rule_var_arr['class'];
							// using model properties
							$model->$field_name = $data[$field_name];
							// using model method (hopper is required!)
							$method = $rule_var_arr['method'];
							if ($model->$method()) {
								$validate = false;
								$this->setError($form, $field_name, $rule_var_arr['msg']);
							}
						}
						break;
					// comparison
					case "compared":
						if (!empty($rule_var_arr['value']) && !empty($data[$field_name]) && empty($_SESSION['validate_err'][$field_name])) {
							switch ($rule_var_arr['type']) {
								case "==":
									if ($data[$field_name] != $rule_var_arr['value']) {
										$validate = false;
										$this->setError($form, $field_name, $rule_var_arr['msg']);
									}
									break;
								case ">":
									if ($data[$field_name] < $rule_var_arr['value']) {
										$validate = false;
										$this->setError($form, $field_name, $rule_var_arr['msg']);
									}
									break;
								case "<":
									if ($data[$field_name] > $rule_var_arr['value']) {
										$validate = false;
										$this->setError($form, $field_name, $rule_var_arr['msg']);
									}
									break;
								case ">=":
									if ($data[$field_name] <= $rule_var_arr['value']) {
										$validate = false;
										$this->setError($form, $field_name, $rule_var_arr['msg']);
									}
									break;
								case "<=":
									if ($data[$field_name] >= $rule_var_arr['value']) {
										$validate = false;
										$this->setError($form, $field_name, $rule_var_arr['msg']);
									}
									break;
							}
						}
						break;
				}
			}
			// setting up CSS class
			if (empty($_SESSION[$form][$field_name.'_err'])) {
				$_SESSION[$form][$field_name.'_cls'] = 'form-control is-valid';
			}
			else {
				$_SESSION[$form][$field_name.'_cls'] = 'form-control is-invalid';
			}
		}
		return $validate;
	}

	/**
     * Sets field error.
     *
     * @return nothing
     */
	public function setError($form, $var, $msg)
	{
		$_SESSION[$form][$var.'_err'] = $msg;
	}
}
