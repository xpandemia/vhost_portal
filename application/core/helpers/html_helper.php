<?php

namespace tinyframe\core\helpers;

use tinyframe\core\helpers\Basic_Helper as Basic_Helper;

class HTML_Helper
{
	/*
		HTML processing
	*/

	/**
     * Creates label.
     *
     * @return string
     */
	public static function setLabel($class, $control, $label)
	{
		return '<label class="'.$class.'" for="'.$control.'">'.$label.'</label>';
	}

	/**
     * Creates input field.
     *
     * @return string
     */
	public static function setInput($type, $class, $control, $help, $placeholder, $value)
	{
		if (!empty($type) && !empty($class) && !empty($control)) {
			return '<input type="'.$type.'" class="'.$class.'"'.
					((isset($help)) ? ' aria-describedby="'.$control.'HelpBlock"' : '').
					' id="'.$control.'" name="'.$control.'"'.
					((isset($placeholder)) ? ' placeholder="'.$placeholder.'"' : '').
					' value="'.$value.'">';
		} else {
			return '<p class="text-danger">HTML_Helper.setInput - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates submit button.
     *
     * @return string
     */
	public static function setSubmit($class, $id, $text)
	{
		if (!empty($class) && !empty($id) && !empty($text)) {
			return '<button type="submit" class="'.$class.'" id="'.$id.'" name="'.$id.'">'.$text.'</button> ';
		} else {
			return '<p class="text-danger">HTML_Helper.setSubmit - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates button.
     *
     * @return string
     */
	public static function setButton($class, $id, $text)
	{
		if (!empty($class) && !empty($id) && !empty($text)) {
			return '<button type="button" class="'.$class.'" id="'.$id.'" name="'.$id.'">'.$text.'</button> ';
		} else {
			return '<p class="text-danger">HTML_Helper.setButton - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates HREF as text.
     *
     * @return string
     */
	public static function setHrefText($controller, $action, $text)
	{
		if (!empty($controller) && !empty($action) && !empty($text)) {
			return '<p><a href="'.Basic_Helper::appUrl($controller, $action).'" class="font-weight-bold text-secondary">'.$text.'</a></p> ';
		} else {
			return '<p class="text-danger">HTML_Helper.setHrefText - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates HREF as button.
     *
     * @return string
     */
	public static function setHrefButton($controller, $action, $class, $text)
	{
		if (!empty($controller) && !empty($action) && !empty($class) && !empty($text)) {
			return '<a href="'.Basic_Helper::appUrl($controller, $action).'" class="'.$class.'">'.$text.'</a> ';
		} else {
			return '<p class="text-danger">HTML_Helper.setHrefButton - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates HREF as icon button.
     *
     * @return string
     */
	public static function setHrefButtonIcon($controller, $action, $class, $icon, $tooltip = null)
	{
		if (!empty($controller) && !empty($action) && !empty($class) && !empty($icon)) {
			return '<a data-toggle="tooltip" title="'.$tooltip.'" href="'.Basic_Helper::appUrl($controller, $action).'" class="'.$class.'"><i class="'.$icon.'"></i></a> ';
		} else {
			return '<p class="text-danger">HTML_Helper.setHrefButtonIcon - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates image.
     *
     * @return string
     */
	public static function setImageLOB($type, $lob, $width = null, $height = null)
	{
		if (!empty($type) && !empty($lob)) {
			return '<br><img class="img-fluid" src="data:'.$type.';base64,'.base64_encode($lob).'" width="'.((empty($width)) ? 460 : $width).'" height="'.((empty($height)) ? 345 : $height).'">';
		} else {
			return '<p class="text-danger">HTML_Helper.setImageLOB - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates GRID from database.
     *
     * @return string
     */
     /* RULES (+ required)
		+ 'model_class' => {MODEL_CLASS},
		+ 'model_method' => {MODEL_METHOD},
		'model_filter' => {MODEL_FILTER},
		'model_filter_var' => {MODEL_FILTER_VAR},
		+ 'grid' => {GRID},
		+ 'controller' => {CONTROLLER},
		+ 'action_add' => {ACTION_ADD},
		'action_edit' => {ACTION_EDIT},
		'action_delete' => {ACTION_DELETE},
		+ 'home_hdr' => {HOME_HEADER}
    */
	public static function setGridDB($rules)
	{
		if (isset($rules) && is_array($rules)) {
			$result = HTML_Helper::setHrefButtonIcon($rules['controller'], $rules['action_add'], 'font-weight-bold', 'far fa-file fa-2x', 'Создать');
			$result .= '<table class="table table-bordered">';
			// using model
			$model = new $rules['model_class'];
			// using model method
			$method = $rules['model_method'];
			/* header */
			$result .= '<tr class="font-italic">';
			$grid = $rules['grid'];
			foreach ($model->$grid() as $key => $value) {
				$result .= '<td><strong>'.$value['name'].'</strong></td>';
			}
			$result .= '</tr>';
			/* data */
			// using model filter
			if (isset($rules['model_filter']) && isset($rules['model_filter_var'])) {
				$filter = $rules['model_filter'];
				$model->$filter = $rules['model_filter_var'];
			}
			// fetching data
			$table = $model->$method();
			if ($table) {
				foreach ($table as $table_row) {
					$result .= '<tr>';
					foreach ($model->$grid() as $key => $value) {
						if ($value['type'] == 'lob') {
							if (!empty($table_row[$key])) {
								$result .= '<td><img class="img-fluid" src="data:'.((isset($table_row['file_type'])) ? $table_row['file_type'] : '').';base64,'.base64_encode( $table_row[$key] ).'" width="80" height="100"></td>';
							} else {
								$result .= '<td>Файл не загружен</td>';
							}
						} else {
							$result .= '<td>'.$table_row[$key].'</td>';
						}
					}
					// actions
					if (isset($table_row['id']) && (isset($rules['action_edit']) || isset($rules['action_delete']))) {
						$result .= '<td>';
						// action edit
						if (isset($rules['action_edit'])) {
							$result .= HTML_Helper::setHrefButtonIcon($rules['controller'], $rules['action_edit'].'/?id='.$table_row['id'].((isset($table_row['pid'])) ? '&pid='.$table_row['pid'] : ''), 'font-weight-bold', 'far fa-edit fa-2x', 'Редактировать');
						}
						// action delete
						if (isset($rules['action_delete'])) {
							$result .= HTML_Helper::setHrefButtonIcon($rules['controller'], $rules['action_delete'].'/?id='.$table_row['id'].((isset($table_row['pid'])) ? '&pid='.$table_row['pid'] : '').'&hdr='.$rules['home_hdr'].'&ctr='.$rules['controller'], 'text-danger font-weight-bold', 'fas fa-times fa-2x', 'Удалить');
						}
						$result .= '</td>';
					}
					$result .= '</tr>';
				}
			}
			$result .= '</table>';
			return $result;
		} else {
			return '<p class="text-danger">HTML_Helper.setGridDB - На входе не массив!</p>';
		}
	}

	/**
     * Creates alert.
     *
     * @return string
     */
	public static function setAlert($msg, $class)
	{
		if (!empty($msg) && !empty($class)) {
			return '<div class="alert '.$class.'">'.$msg.'</div>';
		} else {
			return null;
		}
	}

	/**
     * Creates invalid feedback.
     *
     * @return string
     */
	public static function setInvalidFeedback($err)
	{
		if ($err) {
			return '<div class="invalid-feedback">'.$err.'</div>';
		} else {
			return null;
		}
	}

	/**
     * Creates valid feedback.
     *
     * @return string
     */
	public static function setValidFeedback($err, $msg)
	{
		if (!$err) {
			return '<div class="valid-feedback">'.$msg.'</div>';
		} else {
			return null;
		}
	}
}
