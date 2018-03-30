<?php

namespace tinyframe\core\helpers;

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;
use tinyframe\core\helpers\HTML_Helper as HTML_Helper;

// patterns
define('PATTERN_NUMB', '/^[0-9]*$/u'); // number only
define('PATTERN_ALPHA', '/^[a-zA-Z]*$/u'); // letters only ENG
define('PATTERN_ALPHA_RUS', '/^[ёЁа-яА-Я]*$/u'); // letters only RUS
define('PATTERN_TEXT', '/^[a-zA-Z-\.\,\s]*$/u'); // letters, "-", ".", ",", " " ENG
define('PATTERN_TEXT_RUS', '/^[ёЁа-яА-Я-.,\s]*$/u'); // letters, "-", ".", ",", " " RUS
define('PATTERN_INFO', '/^[a-zA-Z0-9-\.\,\s]*$/u'); // letters, numbers, "-", ".", ",", " " ENG
define('PATTERN_INFO_RUS', '/^[ёЁа-яА-Я0-9-.,\s]*$/u'); // letters, numbers, "-", ".", ",", " " RUS
define('PATTERN_ALPHA_NUMB', '/^[a-zA-Z0-9]*$/u'); // letters and numbers
define('PATTERN_EMAIL_LIGHT', '/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9_\-.]+$/'); // email light
define('PATTERN_EMAIL_STRONG', '/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+$/'); // email strong
define('PATTERN_DATE_LIGHT', '/^(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}$/'); // date DD.MM.YYYY light
define('PATTERN_DATE_STRONG', '/^(?:(?:0[1-9]|1[0-9]|2[0-9]).(?:0[1-9]|1[0-2])|(?:(?:30).(?!02)(?:0[1-9]|1[0-2]))|(?:31.(?:0[13578]|1[02]))).(?:19|20)[0-9]{2}$/'); // date DD.MM.YYYY strong

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

	/* RULES (+ required)
		+ 'type' => {TYPE},
		+ 'class' => {CLASS},
		'format' => {FORMAT},
		'required' => ['default' => 'value', 'msg' => 'Required error message.'],
	    'pattern' => ['value' => {PATTERN_}, 'msg' => 'Pattern error message.'],
	    'width' => ['format' => 'string/numb', 'min' => {min value}, 'max' => {max value}, 'msg' => 'Width error message.'],
	    'unique' => ['class' => 'models\\common\\Model_Class', 'method' => 'Class_Method', 'msg' => 'Unique error message.'],
	    'compared' => ['value' => '{VALUE}', 'type' => '==', 'msg' => 'Compared error message.'],
	    +'success' => 'Success message.'
    */
	public function validate($form, $rules)
	{
		$validate = true;
		// RULES loop
		foreach ($rules as $field_name => $rule_name_arr) {
			if ($form[$field_name.'_vis'] === true) {
				foreach ($rule_name_arr as $rule_name => $rule_var_arr) {
					// RULE processing
					switch ($rule_name) {
						// required check
						case "required":
							if ($rules[$field_name]['type'] === 'checkbox' || $rules[$field_name]['type'] === 'radio') {
								if (!$form[$field_name]) {
									$validate = false;
									$form[$field_name.'_err'] = $rule_var_arr['msg'];
								}
							} else {
								if (empty($form[$field_name])) {
									if (!empty($rule_var_arr['default'])) {
										$form[$field_name] = $rule_var_arr['default'];
										$form[$field_name.'_err'] = '';
									} else {
										$validate = false;
										$form[$field_name.'_err'] = $rule_var_arr['msg'];
									}
								}
							}
							break;
						// pattern check
						case "pattern":
							if (!empty($rule_var_arr['value']) && !empty($form[$field_name]) && empty($form[$field_name.'_err'])) {
								if (!preg_match($rule_var_arr['value'], $form[$field_name])) {
									$validate = false;
									$form[$field_name.'_err'] = $rule_var_arr['msg'];
								}
							}
							break;
						// width check
						case "width":
							if (!empty($form[$field_name]) && empty($form[$field_name.'_err'])) {
								// format
								switch ($rule_var_arr['format']) {
									case "string":
										if (strlen($form[$field_name]) < $rule_var_arr['min'] || strlen($form[$field_name]) > $rule_var_arr['max']) {
											$validate = false;
											$form[$field_name.'_err'] = $rule_var_arr['msg'];
										}
										break;
									case "numb":
										if ($form[$field_name] < $rule_var_arr['min'] || $form[$field_name] > $rule_var_arr['max']) {
											$validate = false;
											$form[$field_name.'_err'] = $rule_var_arr['msg'];
										}
										break;
								}
							}
							break;
						// unique check
						case "unique":
							if (!empty($form[$field_name]) && empty($form[$field_name.'_err'])) {
								// using model
								$model = new $rule_var_arr['class'];
								// using model properties
								$model->$field_name = $form[$field_name];
								// using model method (hopper is required!)
								$method = $rule_var_arr['method'];
								if ($model->$method()) {
									$validate = false;
									$form[$field_name.'_err'] = $rule_var_arr['msg'];
								}
							}
							break;
						// comparison check
						case "compared":
							if (!empty($rule_var_arr['value']) && !empty($form[$field_name]) && empty($form[$field_name.'_err'])) {
								switch ($rule_var_arr['type']) {
									case "==":
										if ($form[$field_name] != $rule_var_arr['value']) {
											$validate = false;
											$form[$field_name.'_err'] = $rule_var_arr['msg'];
										}
										break;
									case ">":
										if ($form[$field_name] < $rule_var_arr['value']) {
											$validate = false;
											$form[$field_name.'_err'] = $rule_var_arr['msg'];
										}
										break;
									case "<":
										if ($form[$field_name] > $rule_var_arr['value']) {
											$validate = false;
											$form[$field_name.'_err'] = $rule_var_arr['msg'];
										}
										break;
									case ">=":
										if ($form[$field_name] <= $rule_var_arr['value']) {
											$validate = false;
											$form[$field_name.'_err'] = $rule_var_arr['msg'];
										}
										break;
									case "<=":
										if ($form[$field_name] >= $rule_var_arr['value']) {
											$validate = false;
											$form[$field_name.'_err'] = $rule_var_arr['msg'];
										}
										break;
								}
							}
							break;
					}
				}
				// setting up CSS class
				if (empty($form[$field_name.'_err'])) {
					$form[$field_name.'_cls'] = $rules[$field_name]['class'].' is-valid';
				}
				else {
					$form[$field_name.'_cls'] = $rules[$field_name]['class'].' is-invalid';
				}
			}
		}
		$form['validate'] = $validate;
		return $form;
	}

	/**
     * Creates form begin.
     *
     * @return string
     */
	public static function setFormBegin($controller, $action, $id, $legend)
	{
		return '<form action="'.Basic_Helper::appUrl($controller, $action).'" method="post" id="'.$id.'" novalidate>
					<legend class="font-weight-bold">'.$legend.'</legend>';
	}

	/**
     * Creates form input.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {INPUT_NAME},
		+ 'control' => {INPUT_ID},
		+ 'type' => {INPUT_TYPE}: text, password, email, etc.,
		+ 'class' => {INPUT_CLASS},
		+ 'required' => yes/no,
		'required_style' => {REQUIRED_STYLE}
		'placeholder' => {INPUT_PLACEHOLDER},
		+ 'value' => {INPUT_VALUE},
		+ 'success' => {INPUT_SUCCESS_MESSAGE},
		+ 'error' => {INPUT_ERROR_MESSAGE},
		'help' => {INPUT_HELP_MESSAGE}
    */
	public static function setFormInput($rules) : string
	{
		if (is_array($rules)) {
			$label = self::setFormLabelStyle($rules['required'], (isset($rules['required_style'])) ? $rules['required_style'] : null, $rules['label']);
			return '<div class="form-group row">'.
						HTML_Helper::setLabel($label['class'], $rules['control'], $label['value']).
						'<div class="col">'.
							HTML_Helper::setInput($rules['type'],
												$rules['class'],
												$rules['control'],
												(isset($rules['help'])) ? $rules['help'] : null,
												(isset($rules['placeholder'])) ? $rules['placeholder'] : null,
												$rules['value']).
							HTML_Helper::setValidFeedback($rules['error'], $rules['success']).
							HTML_Helper::setInvalidFeedback($rules['error']).
							((isset($rules['help'])) ? '<p id="'.$rules['control'].'HelpBlock" class="form-text text-muted">'.$rules['help'].'</p>' : '').
						'</div>
					</div>';
		} else {
			return '<p class="text-danger">Form_Helper.setFormInput - На входе не массив!</p>';
		}
	}

	/**
     * Creates form radio.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {RADIO_NAME},
		+ 'control' => {RADIO_ID},
		+ 'required' => yes/no,
		'required_style' => {REQUIRED_STYLE}
		+ 'radio' => [
						{ID} => [{VALUE} => {LABEL}]
					]
		+ 'value' => {RADIO_VALUE},
		+ 'error' => {RADIO_ERROR_MESSAGE}
    */
	public static function setFormRadio($rules) : string
	{
		if (is_array($rules)) {
			$label = self::setFormLabelStyle($rules['required'], (isset($rules['required_style'])) ? $rules['required_style'] : null, $rules['label']);
			$result = '<div class="form-group">'.
						HTML_Helper::setLabel($label['class'], $rules['control'], $label['value']).
						'<div class="col">';
			if (is_array($rules['radio'])) {
				foreach ($rules['radio'] as $radio_id => $radio) {
					foreach ($radio as $radio_value => $radio_label) {
						$result .= '<label class="radio-inline"><input type="radio" id="'.$radio_id.'" name="'.$rules['control'].'" value="'.$radio_value.'"';
							if (isset($rules['value']) && $rules['value'] == $radio_value) {
								$result .= ' checked';
							}
							$result .= '>'.$radio_label.'</label> ';
					}
				}
				if ($rules['error']) {
					$result .= '<p class="text-danger">'.$rules['error'].'</p>';
				}
				return $result;
			} else {
				return '<p class="text-danger">Form_Helper.setFormRadio - Состав RADIO не массив!</p>';
			}
		} else {
			return '<p class="text-danger">Form_Helper.setFormRadio - На входе не массив!</p>';
		}
	}

	/**
     * Creates form checkbox.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {CHECKBOX_NAME},
		+ 'control' => {CHECKBOX_ID},
		+ 'class' => {CHECKBOX_CLASS},
		+ 'value' => {CHECKBOX_VALUE},
		+ 'success' => {CHECKBOX_SUCCESS_MESSAGE},
		+ 'error' => {CHECKBOX_ERROR_MESSAGE}
    */
	public static function setFormCheckbox($rules) : string
	{
		if (is_array($rules)) {
			return '<div class="form-check">
						<div class="col">
							<input type="checkbox" class="'.$rules['class'].'" id="'.$rules['control'].'" name="'.$rules['control'].'" '.$rules['value'].'><b>'.$rules['label'].'</b>'.
							HTML_Helper::setValidFeedback($rules['error'], $rules['success']).
							HTML_Helper::setInvalidFeedback($rules['error']).
						'</div>
					</div>';
		} else {
			return '<p class="text-danger">Form_Helper.setFormCheckbox - На входе не массив!</p>';
		}
	}

	/**
     * Creates form select list based on SQL-query.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {SELECTLIST_NAME},
		+ 'control' => {SELECTLIST_ID},
		+ 'class' => {SELECTLIST_CLASS},
		+ 'required' => yes/no,
		'required_style' => {SELECTLIST_STYLE}
		+ 'model_class' => {MODEL_CLASS},
		+ 'model_method' => {MODEL_METHOD},
		+ 'model_field' => {MODEL_FIELD},
		'model_field_name' => {MODEL_FIELD_NAME},
		+ 'value' => {SELECTLIST_VALUE},
		+ 'success' => {SELECTLIST_SUCCESS_MESSAGE},
		+ 'error' => {SELECTLIST_ERROR_MESSAGE}
    */
	public static function setFormSelectListDB($rules) : string
	{
		if (is_array($rules)) {
			$label = self::setFormLabelStyle($rules['required'], (isset($rules['required_style'])) ? $rules['required_style'] : null, $rules['label']);
			$result = '<div class="form-group">'.
						HTML_Helper::setLabel($label['class'], $rules['control'], $label['value']).
						'<select class="'.$rules['class'].'" id="'.$rules['control'].'" name="'.$rules['control'].'">';
			// using model
			$model = new $rules['model_class'];
			// using model method (hopper is required!)
			$method = $rules['model_method'];
			// fetching data
			$table = $model->$method();
			// making select list
			$result .= '<option value=""'.(empty($rules['value']) ? ' selected' : '').'></option>';
			foreach ($table as $row) {
				$result .= '<option value="'.$row[$rules['model_field']].'"'.
							(($rules['value'] === $row[$rules['model_field']]) ? ' selected' : '').'>'.
							((isset($rules['model_field_name'])) ? $row[$rules['model_field_name']] : $row[$rules['model_field']]).
							'</option>';
			}
			$result .= '</select>'.
						HTML_Helper::setValidFeedback($rules['error'], $rules['success']).
						HTML_Helper::setInvalidFeedback($rules['error']).
						'</div>';
			return $result;
		} else {
			return '<p class="text-danger">Form_Helper.setFormSelectListDB - На входе не массив!</p>';
		}
	}

	/**
     * Creates form CAPTCHA.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'action' => {CAPTCHA_REFRESH_ACTION},
		+ 'class' => {CAPTCHA_CODE_CLASS},
		+ 'value' => {CAPTCHA_CODE_VALUE},
		+ 'success' => {CAPTCHA_SUCCESS_MESSAGE},
		+ 'error' => {CAPTCHA_ERROR_MESSAGE}
    */
	public static function setFormCaptcha($rules) : string
	{
		if (is_array($rules)) {
			return '<img id="img-captcha" src="/images/temp/captcha/captcha_'.session_id().'.png">
						<a href="/'.BEHAVIOR.'/'.$rules['action'].'" class="btn btn-primary"><i class="fas fa-sync"></i> Обновить</a>
						<div class="form-group">
							<label id="label-captcha" for="captcha" class="control-label font-weight-bold">Пожалуйста, введите указанный на изображении код:</label>
								<input id="captcha" name="captcha" type="text" class="'.$rules['class'].'" value="'.$rules['value'].'">'.
								HTML_Helper::setValidFeedback($rules['error'], $rules['success']).
								HTML_Helper::setInvalidFeedback($rules['error']).
						'</div>';
		} else {
			return '<p class="text-danger">Form_Helper.setFormCaptcha - На входе не массив!</p>';
		}
	}

	/**
     * Creates form sub header.
     *
     * @return string
     */
	public static function setFormHeaderSub($header)
	{
		return '<hr><h5>'.$header.'</h5><br>';
	}

	/**
     * Creates form label style.
     *
     * @return string
     */
    /* Required style
		'StarUp' - bold label with following top_align '*'
		'RedBold' - bold red coloured label
		default - normal italic
    */
	public static function setFormLabelStyle($required, $required_style, $value) : array
	{
		if ($required === 'yes') {
			switch ($required_style) {
				case 'StarUp':
					$label['class'] = 'font-weight-bold';
					$label['value'] = $value.' <span style="vertical-align: text-top">*</span>';
					break;
				case 'RedBold':
					$label['class'] = 'text-danger font-weight-bold';
					$label['value'] = $value;
					break;
				default:
					$label['class'] = 'font-italic';
					$label['value'] = $value;
			}
		} else {
			$label['class'] = 'font-weight-bold';
			$label['value'] = $value;
		}
		return $label;
	}

	/**
     * Creates form end.
     *
     * @return string
     */
	public static function setFormEnd() : string
	{
		return '</form>';
	}
}
