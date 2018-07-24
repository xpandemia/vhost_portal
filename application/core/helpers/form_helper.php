<?php

namespace tinyframe\core\helpers;

use PHPMailer\PHPMailer\Exception;

// patterns
// numbers
define('PATTERN_NUMB', '/^[0-9]*$/u');
define('MSG_NUMB', 'только цифры');
// numbers, "-"
define('PATTERN_INILA', '/^[0-9-]*$/u');
define('MSG_INILA', 'только цифры и тире');
// letters ENG
define('PATTERN_ALPHA', '/^[a-zA-Z]*$/u');
define('MSG_ALPHA', 'только латинские буквы');
// letters RUS
define('PATTERN_ALPHA_RUS', '/^[ёЁа-яА-Я]*$/u');
define('MSG_ALPHA_RUS', 'только русские буквы');
// letters RUS, "-"
define('PATTERN_FAMILY_RUS', '/^[ёЁа-яА-Я-]*$/u');
define('MSG_FAMILY_RUS', 'только русские буквы и тире');
// letters ENG, "-", ".", ",", spaces
define('PATTERN_TEXT', '/^[a-zA-Z-\.\,\s]*$/u');
define('MSG_TEXT', 'только латинские буквы, тире, точки, запятые и пробелы');
// letters RUS, "-", ".", ",", spaces
define('PATTERN_TEXT_RUS', '/^[ёЁа-яА-Я-.,\s]*$/u');
define('MSG_TEXT_RUS', 'только русские буквы, тире, точки, запятые и пробелы');
// letters ENG, numbers, "-", ".", ",", "#", spaces
define('PATTERN_INFO', '/^[a-zA-Z0-9-\.\,\#\/\s]*$/u');
define('MSG_INFO', 'только латинские буквы, цифры, тире, точки, запятые, символ # и пробелы');
// letters RUS, numbers, "-", ".", ",", "№", spaces
define('PATTERN_INFO_RUS', '/^[a-zA-ZёЁа-яА-Я0-9-.,\(\)№\/\s\«\»\'\"\:\;\_\-\#]*$/u');
define('MSG_INFO_RUS', 'только русские буквы, цифры, тире, точки, запятые, символ № и пробелы');
// letters ENG and numbers
define('PATTERN_ALPHA_NUMB', '/^[a-zA-Z0-9]*$/u');
define('MSG_ALPHA_NUMB', 'только латинские буквы и цифры');
// letters RUS and numbers
define('PATTERN_ALPHA_NUMB_RUS', '/^[ёЁа-яА-Я0-9]*$/u');
define('MSG_ALPHA_NUMB_RUS', 'только русские буквы и цифры');
// letters and numbers
define('PATTERN_ALPHA_NUMB_ALL', '/^[a-zA-ZёЁа-яА-Я0-9\s\-\.\,\_\/]*$/u');
define('MSG_ALPHA_NUMB_ALL', 'только буквы, цифры, пробелы, нижнее подчёркивание, пробелы и знаки препинания');
// letters ENG, numbers, "-", spaces, ".", ",", "'", "«", "»"
define('PATTERN_SPEC', '/^[a-zA-Z0-9-\s\.\,\'\«\»]*$/u');
define('MSG_SPEC', 'только латинские буквы, цифры, пробелы и знаки препинания');
// letters RUS, numbers, "-", spaces, ".", ",", "'", "«", "»"
define('PATTERN_SPEC_RUS', '/^[ёЁа-яА-Я0-9-\s\.\,\'\«\»]*$/u');
define('MSG_SPEC_RUS', 'только русские буквы, цифры, пробелы и знаки препинания');
// email light
define('PATTERN_EMAIL_LIGHT', '/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9_\-.]+$/');
define('MSG_EMAIL_LIGHT', 'в формате user@domain');
// email strong
define('PATTERN_EMAIL_STRONG', '/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9_\-]+$/');
// numbers, "(", ")"
define('PATTERN_PHONE_HOME', '/^[0-9-()]*$/u');
define('MSG_PHONE_HOME', 'только цифры и круглые скобки');
// letters RUS, numbers, ",", spaces
define('PATTERN_PHONE_ADD', '/^[ёЁа-яА-Я0-9,\s\;\.\/]*$/u');
define('MSG_PHONE_ADD', 'только русские буквы, цифры, запятые и пробелы');
// date DD.MM.YYYY light
define('PATTERN_DATE_LIGHT', '/^(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}$/');
define('MSG_DATE_LIGHT', 'в формате ДД.ММ.ГГГГ');
// date DD.MM.YYYY strong
define('PATTERN_DATE_STRONG', '/^(?:(?:0[1-9]|1[0-9]|2[0-9]).(?:0[1-9]|1[0-2])|(?:(?:30).(?!02)(?:0[1-9]|1[0-2]))|(?:31.(?:0[13578]|1[02]))).(?:19|20)[0-9]{2}$/');
define('MSG_DATE_STRONG', 'в формате ДД.ММ.ГГГГ и только 20-го, 21-го вв');

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
		+ 'type' => {text, date, file, selectlist, checkbox, radio},
		+ 'class' => {CLASS},
		'format' => {FORMAT},
		'required' => ['default' => 'value', 'msg' => 'Required error message.'],
	    'pattern' => ['value' => {PATTERN_}, 'msg' => 'Pattern error message.'],
	    'width' => ['format' => 'string/numb', 'min' => {min value}, 'max' => {max value}, 'msg' => 'Width error message.'],
	    'unique' => ['class' => 'models\\common\\Model_Class', 'method' => 'Class_Method', 'msg' => 'Unique error message.'],
	    'compared' => ['value' => '{VALUE}', 'type' => {'==', '>', '<', '>=', '<='}, 'msg' => 'Compared error message.'],
	    'size' = ['value' => {FILE_SIZE}, 'msg' => 'File size error message.'],
	    'ext' => ['value' => {FILE_EXTENSION}, 'msg' => 'File extension error message.'],
	    +'success' => 'Success message.'
    */
	public function validate($form, $rules)
	{
		$validate = true;
		// RULES loop
		foreach ($rules as $field_name => $rule_name_arr) {
			if ($form[$field_name . '_vis'] === true) {
				foreach ($rule_name_arr as $rule_name => $rule_var_arr) {
					if (!isset($form[$field_name])) {
						continue;
					}
					// RULE processing
					switch ($rule_name) {
						// required check
						case 'required':
							switch ($rules[$field_name]['type']) {
								case 'checkbox':
									if ($form[$field_name] != 'checked') {
										$validate = false;
										$form[$field_name . '_err'] = $rule_var_arr['msg'];
									}
									break;
								case 'radio':
									if (!$form[$field_name] && $form[$field_name] != '0') {
										$validate = false;
										$form[$field_name . '_err'] = $rule_var_arr['msg'];
									}
									break;
								default:
									if (!$form[$field_name] && $form[$field_name] != '0') {
										if (!empty($rule_var_arr['default'])) {
											$form[$field_name] = $rule_var_arr['default'];
											$form[$field_name . '_err'] = '';
										}
										else {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
									}
							}
							break;
						// pattern check
						case 'pattern':
							if (!empty($rule_var_arr['value']) && !empty($form[$field_name]) && empty($form[$field_name . '_err'])) {
								if (!preg_match($rule_var_arr['value'], $form[$field_name])) {
									$validate = false;
									$form[$field_name . '_err'] = $rule_var_arr['msg'];
								}
							}
							break;
						// width check
						case 'width':
							if (!empty($form[$field_name]) && empty($form[$field_name . '_err'])) {
								// format
								switch ($rule_var_arr['format']) {
									case 'string':
										if (mb_strlen($form[$field_name]) < $rule_var_arr['min'] || mb_strlen($form[$field_name]) > $rule_var_arr['max']) {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
										break;
									case 'numb':
										if ($form[$field_name] < $rule_var_arr['min'] || $form[$field_name] > $rule_var_arr['max']) {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
										break;
								}
							}
							break;
						// unique check
						case 'unique':
							if (!empty($form[$field_name]) && empty($form[$field_name . '_err'])) {
								// using model
								$model = new $rule_var_arr['class'];
								// using model properties
								$model->$field_name = $form[$field_name];
								// using model method
								$method = $rule_var_arr['method'];
								if ($model->$method()) {
									$validate = false;
									$form[$field_name . '_err'] = $rule_var_arr['msg'];
								}
							}
							break;
						// comparison check
						case 'compared':
							if (!empty($rule_var_arr['value']) && !empty($form[$field_name]) && empty($form[$field_name . '_err'])) {
								if ($rules[$field_name]['type'] == 'date') {
									$field_value = date('Y-m-d', strtotime($form[$field_name]));
									$test_value = date('Y-m-d', strtotime($rule_var_arr['value']));
								}
								else {
									$field_value = $form[$field_name];
									$test_value = $rule_var_arr['value'];
								}
								switch ($rule_var_arr['type']) {
									case '==':
										if ($field_value != $test_value) {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
										break;
									case '>':
										if ($field_value <= $test_value) {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
										break;
									case '<':
										if ($field_value >= $test_value) {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
										break;
									case '>=':
										if ($field_value < $test_value) {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
										break;
									case '<=':
										if ($field_value > $test_value) {
											$validate = false;
											$form[$field_name . '_err'] = $rule_var_arr['msg'];
										}
										break;
								}
							}
							break;
						// file name check
						case 'name':
							if ($rules[$field_name]['type'] == 'file' && isset($form[$field_name . '_name'])) {
								if (mb_strlen($form[$field_name . '_name']) > $rule_var_arr['value']) {
									unset($form[$field_name]);
									unset($form[$field_name . '_name']);
									unset($form[$field_name . '_type']);
									unset($form[$field_name . '_size']);
									$validate = false;
									$form[$field_name . '_err'] = $rule_var_arr['msg'];
								}
							}
							break;
						// file size check
						case 'size':
							if ($rules[$field_name]['type'] == 'file' && isset($form[$field_name . '_size'])) {
								if ($form[$field_name . '_size'] === 0) {
									unset($form[$field_name]);
									unset($form[$field_name . '_name']);
									unset($form[$field_name . '_type']);
									unset($form[$field_name . '_size']);
									$validate = false;
									$form[$field_name . '_err'] = 'Выбран пустой файл!';
								} elseif (Files_Helper::getSize($form[$field_name . '_size'], FILES_SIZE['size']) > $rule_var_arr['value']) {
									unset($form[$field_name]);
									unset($form[$field_name . '_name']);
									unset($form[$field_name . '_type']);
									unset($form[$field_name . '_size']);
									$validate = false;
									$form[$field_name . '_err'] = $rule_var_arr['msg'];
								}
							}
							break;
						// file extension check
						case 'ext':
							if ($rules[$field_name]['type'] == 'file' && isset($form[$field_name . '_type']) && isset($form[$field_name])) {
								if (!in_array($form[$field_name . '_type'], $rule_var_arr['value'])) {
									unset($form[$field_name]);
									unset($form[$field_name . '_name']);
									unset($form[$field_name . '_type']);
									unset($form[$field_name . '_size']);
									$validate = false;
									$form[$field_name . '_err'] = $rule_var_arr['msg'];
								} elseif (empty($form[$field_name . '_id'])) {
									// file format check
									if (!in_array(mime_content_type($form[$field_name]), $rule_var_arr['value'])) {
										unset($form[$field_name]);
										unset($form[$field_name . '_name']);
										unset($form[$field_name . '_type']);
										unset($form[$field_name . '_size']);
										$validate = false;
										$form[$field_name . '_err'] = $rule_var_arr['msg'];
									}
								}
							}
							break;
					}
				}
				// setting up CSS class
				if (empty($form[$field_name . '_err'])) {
					$form[$field_name . '_cls'] = $rules[$field_name]['class'] . ' is-valid';
				}
				else {
					$form[$field_name . '_cls'] = $rules[$field_name]['class'] . ' is-invalid';
				}
			}
		}
		$form['validate'] = $validate;
		return $form;
	}

	/**
     * Creates form begin.
     * $home: 0 - no, 1 - setHrefButton, 2 - setHrefButtonIcon
     *
     * @return string
     */
	public static function setFormBegin($controller, $action, $id, $legend, $home = 0, $logo = null)
	{
		switch ($home) {
			case 1:
				$header = '<div class=""><legend class="font-weight-bold">'.$legend.'</legend></div>';
				$home_button = '<div class="col text-left">'.HTML_Helper::setHrefButton('Main', 'Index', 'btn btn-primary', 'На главную').'</div>';
				break;
			case 2:
				$header = '<div class=""><legend class="font-weight-bold">'.$legend.'</legend></div>';
				$home_button = '<div class="col">'.HTML_Helper::setHrefButtonIcon('Main', 'Index', 'btn btn-primary', 'fas fa-home', 'На главную').'</div>';
				break;
			default:
				$header = '<div class="col text-left"><legend class="font-weight-bold">'.$legend.'</legend></div>';
				$home_button = '';
		}
		return '<form enctype="multipart/form-data" action="'.Basic_Helper::appUrl($controller, $action).'" method="post" id="'.$id.'" novalidate>'.
					'<div class="form-group row">'.
					$header.
					$home_button.
					((!empty($logo)) ? '<div class="col text-right"><img src="'.$logo.'" alt="Logo" style="width:25%;heigth:25%"></div>' : '').
					'</div>';
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
		if (isset($rules) && is_array($rules)) {
			$label = self::setFormLabelStyle($rules['required'], (isset($rules['required_style'])) ? $rules['required_style'] : null, $rules['label']);
			return '<div class="form-group row" id="'.$rules['control'].'_div">'.
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
     * Creates form input text.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {TEXT_NAME},
		+ 'control' => {TEXT_ID},
		+ 'value' => {TEXT_VALUE}
    */
	public static function setFormInputText($rules) : string
	{
		if (isset($rules) && is_array($rules)) {
			return '<div class="form-group row">'.
						'<label class="font-weight-bold" for="'.$rules['control'].'">'.$rules['label'].'</label>'.
						'<div class="col">'.
							'<input type="text" class="form-control" id="'.$rules['control'].'" name="'.$rules['control'].'" value="'.$rules['value'].'">'.
						'</div>'.
					'</div>';
		} else {
			return '<p class="text-danger">Form_Helper.setFormInputSimple - На входе не массив!</p>';
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
		if (isset($rules) && is_array($rules)) {
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
		if (isset($rules) && is_array($rules)) {
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
     * Creates form select list.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {SELECTLIST_NAME},
		+ 'control' => {SELECTLIST_ID},
		+ 'class' => {SELECTLIST_CLASS},
		+ 'required' => yes/no,
		'required_style' => {SELECTLIST_STYLE}
		+ 'source' => {SELECTLIST_SOURCE} : ['code' => code, 'description' => description],
		+ 'value' => {SELECTLIST_VALUE},
		+ 'success' => {SELECTLIST_SUCCESS_MESSAGE},
		+ 'error' => {SELECTLIST_ERROR_MESSAGE}
    */
	public static function setFormSelectList($rules) : string
	{
		if (isset($rules) && is_array($rules)) {
			$label = self::setFormLabelStyle($rules['required'], (isset($rules['required_style'])) ? $rules['required_style'] : null, $rules['label']);
			$result = '<div class="form-group row" id="'.$rules['control'].'_div">'.
						HTML_Helper::setLabel($label['class'], $rules['control'], $label['value']).
						'<div class="col">'.
						'<select class="'.$rules['class'].'" id="'.$rules['control'].'" name="'.$rules['control'].'">';
			if (isset($rules['source']) && is_array($rules['source'])) {
				// making select list
				$result .= '<option value=""'.(empty($rules['value']) ? ' selected' : '').'></option>';
				foreach ($rules['source'] as $row) {
					if (isset($row['code'])) {
						$result .= '<option value="'.$row['code'].'"'.
									(($rules['value'] == $row['code']) ? ' selected' : '').'>'.
									((isset($row['description'])) ? $row['description'] : $row['code']).
									'</option>';
					} else {
						return '<p class="text-danger">Form_Helper.setFormSelectList - В источнике не указан код!</p>';
					}
				}
			} else {
				// making blank select list
				$result .= '<option value=""'.(empty($rules['value']) ? ' selected' : '').'></option>';
			}
			$result .= '</select>'.
						HTML_Helper::setValidFeedback($rules['error'], $rules['success']).
						HTML_Helper::setInvalidFeedback($rules['error']).
						'</div>'.
						'</div>';
			return $result;
		} else {
			return '<p class="text-danger">Form_Helper.setFormSelectList - На входе не массив!</p>';
		}
	}

	/**
     * Creates form select list blank.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {SELECTLIST_NAME},
		+ 'control' => {SELECTLIST_ID}
    */
	public static function setFormSelectListBlank($rules) : string
	{
		if (isset($rules) && is_array($rules)) {
			return '<div class="form-group row">'.
							'<label class="font-weight-bold" for="'.$rules['control'].'">'.$rules['label'].'</label>'.
							'<div class="col">'.
								'<select class="form-control" id="'.$rules['control'].'" name="'.$rules['control'].'"></select>'.
							'</div>'.
						'</div>';
		} else {
			return '<p class="text-danger">Form_Helper.setFormSelectListBlank - На входе не массив!</p>';
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
		'model_filter' => {MODEL_FILTER},
		'model_filter_val' => {MODEL_FILTER_VALUE},
		+ 'value' => {SELECTLIST_VALUE},
		+ 'success' => {SELECTLIST_SUCCESS_MESSAGE},
		+ 'error' => {SELECTLIST_ERROR_MESSAGE}
    */
	public static function setFormSelectListDB($rules) : string
	{
		if (isset($rules) && is_array($rules)) {
			$label = self::setFormLabelStyle($rules['required'], (isset($rules['required_style'])) ? $rules['required_style'] : null, $rules['label']);
			$result = '<div class="form-group row" id="'.$rules['control'].'_div">'.
						HTML_Helper::setLabel($label['class'], $rules['control'], $label['value']).
						'<div class="col">'.
						'<select class="'.$rules['class'].'" id="'.$rules['control'].'" name="'.$rules['control'].'">';
			if (isset($rules['model_class']) && !empty($rules['model_class']) && isset($rules['model_method']) && !empty($rules['model_method'])) {
				// using model
				$model = new $rules['model_class'];
				// using model method
				$method = $rules['model_method'];
				// using filter
				if (isset($rules['model_filter'])) {
					$filter = $rules['model_filter'];
					$model->$filter = $rules['model_filter_val'];
				}
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
			} else {
				// making blank select list
				$result .= '<option value=""'.(empty($rules['value']) ? ' selected' : '').'></option>';
			}
			$result .= '</select>'.
						HTML_Helper::setValidFeedback($rules['error'], $rules['success']).
						HTML_Helper::setInvalidFeedback($rules['error']).
						'</div>'.
						'</div>';
			return $result;
		} else {
			return '<p class="text-danger">Form_Helper.setFormSelectListDB - На входе не массив!</p>';
		}
	}

	/**
     * Creates form select list KLADR.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {SELECTLIST_NAME},
		+ 'control' => {SELECTLIST_ID},
		+ 'model_class' => {MODEL_CLASS},
		+ 'model_method' => {MODEL_METHOD},
		'model_filter' => {MODEL_FILTER},
		'model_filter_val' => {MODEL_FILTER_VALUE},
		+ 'value' => {SELECTLIST_VALUE}
    */
	public static function setFormSelectListKladr($rules) : string
	{
		if (isset($rules) && is_array($rules)) {
			$result = '<div class="form-group row">'.
						'<label class="font-weight-bold" for="'.$rules['control'].'">'.$rules['label'].'</label>'.
							'<div class="col">'.
							'<select class="form-control" id="'.$rules['control'].'" name="'.$rules['control'].'">';
			// using model
			$model = new $rules['model_class'];
			// using model method
			$method = $rules['model_method'];
			// using filter
			if (isset($rules['model_filter'])) {
				$filter = $rules['model_filter'];
				$model->$filter = $rules['model_filter_val'];
			}
			// fetching data
			$table = $model->$method();
			$result .= '<option value=""'.(empty($rules['value']) ? ' selected' : '').'></option>';
			foreach ($table as $row) {
				$result .= '<option value="'.$row['kladr_code'].'"'.
							(($rules['value'] === $row['kladr_code']) ? ' selected' : '').'>'.
							$row['kladr_name'].' '.$row['kladr_abbr'].
							'</option>';
			}
			$result .= '</select>'.
						'</div>'.
						'</div>';
			return $result;
		} else {
			return '<p class="text-danger">Form_Helper.setFormSelectListKladr - На входе не массив!</p>';
		}
	}

	/**
     * Creates form file.
     *
     * @return string
     */
    /* RULES (+ required)
		+ 'label' => {FILE_NAME},
		+ 'control' => {FILE_ID},
		'required' => {FILE_REQUIRED},
		'required_style' => {FILE_STYLE},
		+ 'data' => {FILE_DATA},
		'sample' => {SAMPLE},
		'home_id' => {HOME_ID},
		+ 'home_hdr' => {HOME_HEADER},
		+ 'home_ctr' => {HOME_CONTROLLER},
		+ 'home_act' => {HOME_ACTION},
		+ 'ext' => {ALLOWED_EXTENSIONS}
    */
    public static function setFormFile($rules) : string
    {
		if (isset($rules) && is_array($rules)) {
			$field = $rules['control'];
			$result = '<div class="form-group" id="'.$field.'_div">';
			// label
			$label = self::setFormLabelStyle($rules['required'], (isset($rules['required_style'])) ? $rules['required_style'] : null, $rules['label']);
			$result .= '<div class="col">'.
						HTML_Helper::setLabel($label['class'], $field, $label['value']).'</div>';
			// set limits
			$result .= '<div class="col">'.
							'<p class="font-weight-bold font-italic">Допустимый размер файла: '.FILES_SIZE['value'].' '.FILES_SIZE['size'].'</p>'.
							'<p class="font-weight-bold font-italic">Допустимые расширения файла: '.strtoupper(implode(', ', array_keys($rules['ext']))).'</p>'.
						'</div>';
			// set help
			$result .= '<div class="col">'.
							'<div class="alert alert-info">'.nl2br("Чтобы <strong>отменить загрузку файла</strong>, нажмите <strong>\"Выберите файл\"</strong>, затем <strong>\"Отмена\"</strong> в открывшемся диалоге выбора файла.\nЧтобы <strong>удалить загруженный файл</strong>, нажмите <i class=\"fas fas fa-times\"></i>.").'</div>'.
						'</div>';
			// sample
			if (isset($rules['sample'])) {
				$result .= '<div class="col">'.
							'<a href="'.$rules['sample'].'">Образец можно скачать здесь</a><p></p>'.
							'</div>';
			}
			// file
			if (isset($rules['data'][$field.'_id'])) {
				$result .= '<input type="hidden" id="'.$field.'_id" name="'.$field.'_id" value="'.$rules['data'][$field.'_id'].'"/>'.
							'<span style="padding-left:10px;"> </span><img class="img-fluid" src="data:'.$rules['data'][$field.'_type'].';base64,'.base64_encode( $rules['data'][$field] ).'" width="80" height="100">'.
							'<span style="padding-left:10px;"> </span>'.
							HTML_Helper::setHrefButtonIcon('Scans', 'Show/?id='.$rules['data'][$field.'_id'].'&ctr='.$rules['home_ctr'].'&act='.$rules['home_act'], 'font-weight-bold', 'far fa-file-image fa-2x', 'Просмотреть файл').
							'<span style="padding-left:10px;"> </span>'.
							HTML_Helper::setHrefButtonIcon('Scans', 'DeleteConfirm/?id='.$rules['data'][$field.'_id'].((isset($rules['home_id']) && !empty($rules['home_id'])) ? '&pid='.$rules['home_id'] : '').'&hdr='.$rules['home_hdr'].'&ctr='.$rules['home_ctr'].'&act='.$rules['home_act'], 'text-danger font-weight-bold', 'fas fa-times fa-2x', 'Удалить файл');
			} else {
				$result .= '<span style="padding-left:10px;"> </span><input type="file" id="'.$field.'" name="'.$field.'"/>';
			}
			// feedback
			if ($rules['data'][$field.'_err']) {
				$result .= '<p class="text-danger">'.$rules['data'][$field.'_err'].'</p>';
			}
			$result .= '<p></p>';
			$result .= '</div>';
			return $result;
		} else {
			return '<p class="text-danger">Form_Helper.setFormFile - На входе не массив!</p>';
		}
	}

	/**
     * Creates form file list based on SQL-query.
     *
     * @return string
     */
    /* RULES (+ required)
		'id' => {ID},
		'required' => {REQUIRED_FIELD}
		'required_style' => {FILELIST_REQUIRED_STYLE},
		+ 'model_class' => {MODEL_CLASS},
		+ 'model_method' => {MODEL_METHOD},
		'model_filter' => {MODEL_FILTER},
		'model_filter_var' => {MODEL_FILTER_VAR},
		+ 'model_field' => {MODEL_FIELD},
		+ 'model_field_name' => {MODEL_FIELD_NAME},
		+ 'data' => {FILELIST_DATA},
		'home_id' => {HOME_ID},
		+ 'home_hdr' => {HOME_HEADER},
		+ 'home_ctr' => {HOME_CONTROLLER},
		+ 'home_act' => {HOME_ACTION},
		+ 'ext' => {ALLOWED_EXTENSIONS}
    */
    public static function setFormFileListDB($rules) : string
    {
		if (isset($rules) && is_array($rules)) {
			if (isset($rules['id'])) {
				$result = '<div class="form-group" id="'.$rules['id'].'">';
			} else
				{
					$result = '<div class="form-group">';
				}
			// set limits
			$result .= '<div class="col">'.
							'<p class="font-weight-bold font-italic">Допустимый размер файлов: '.FILES_SIZE['value'].' '.FILES_SIZE['size'].'</p>'.
							'<p class="font-weight-bold font-italic">Допустимые расширения файлов: '.strtoupper(implode(', ', array_keys($rules['ext']))).'</p>'.
						'</div>';
			// set help
			$result .= '<div class="col">'.
							'<div class="alert alert-info">'.nl2br("Чтобы <strong>отменить загрузку файла</strong>, нажмите <strong>\"Выберите файл\"</strong>, затем <strong>\"Отмена\"</strong> в открывшемся диалоге выбора файла.\nЧтобы <strong>удалить загруженный файл</strong>, нажмите <i class=\"fas fas fa-times\"></i>.").'</div>'.
						'</div>';
			// using model
			$model = new $rules['model_class'];
			// using model method
			$method = $rules['model_method'];
			// using model filter
			if (isset($rules['model_filter']) && isset($rules['model_filter_var'])) {
				$filter = $rules['model_filter'];
				$model->$filter = $rules['model_filter_var'];
			}
			// fetching data
			$table = $model->$method();
			// making file list
			foreach ($table as $row) {
				$field = $row[$rules['model_field']];
				$result .= '<div class="col" id="'.$field.'_div">';
				// next label
				if (isset($rules['required'])) {
					if ($row[$rules['required']] == 1) {
						$label = self::setFormLabelStyle('yes', (isset($rules['required_style'])) ? $rules['required_style'] : null, $row[$rules['model_field_name']]);
					} else {
						$label = self::setFormLabelStyle('no', (isset($rules['required_style'])) ? $rules['required_style'] : null, $row[$rules['model_field_name']]);
					}
					$result .= HTML_Helper::setLabel($label['class'], $field, $label['value']);
				} else {
					$result .= HTML_Helper::setLabel('font-weight-bold', $field, $row[$rules['model_field_name']]);
				}
				// next input
				if (isset($rules['data'][$field.'_id'])) {
					// file exists
					$result .= '<input type="hidden" id="'.$field.'_id" name="'.$field.'_id" value="'.$rules['data'][$field.'_id'].'"/>'.
								'<span style="padding-left:10px;"> </span><img class="img-fluid" src="data:'.$rules['data'][$field.'_type'].';base64,'.base64_encode( $rules['data'][$field] ).'" width="80" height="100">'.
								'<span style="padding-left:10px;"> </span>'.
								HTML_Helper::setHrefButtonIcon('Scans', 'Show/?id='.$rules['data'][$field.'_id'].((isset($rules['home_id']) && !empty($rules['home_id'])) ? '&pid='.$rules['home_id'] : '').'&ctr='.$rules['home_ctr'].'&act='.$rules['home_act'], 'font-weight-bold', 'far fa-file-image fa-2x', 'Посмотреть файл').
								'<span style="padding-left:10px;"> </span>'.
								HTML_Helper::setHrefButtonIcon('Scans', 'DeleteConfirm/?id='.$rules['data'][$field.'_id'].((isset($rules['home_id']) && !empty($rules['home_id'])) ? '&pid='.$rules['home_id'] : '').'&hdr='.$rules['home_hdr'].'&ctr='.$rules['home_ctr'].'&act='.$rules['home_act'], 'text-danger font-weight-bold', 'fas fa-times fa-2x', 'Удалить файл').'</div>';
				} else {
					// file not exists
					$result .= '<span style="padding-left:10px;"> </span><input type="file" id="'.$field.'" name="'.$field.'"/></div>';
				}
				// feedback
				if ($rules['data'][$field.'_err']) {
					$result .= '<p class="text-danger">'.$rules['data'][$field.'_err'].'</p>';
				}
				$result .= '<p></p>';
			}
			$result .= '</div>';
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
		if (isset($rules) && is_array($rules)) {
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
