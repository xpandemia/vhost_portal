<?php

namespace tinyframe\core\helpers;

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
		if (!empty($control) && !empty($label)) {
			return '<label class="'.$class.'" for="'.$control.'">'.$label.'</label>';
		} else {
			return '<p class="text-danger">HTML_Helper.setLabel - На входе недостаточно данных!</p>';
		}
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
     * Creates HREF as text.
     *
     * @return string
     */
	public static function setHrefText($href, $text)
	{
		if (!empty($href) && !empty($text)) {
			return '<p><a href="/'.BEHAVIOR.'/'.$href.'" class="font-weight-bold text-secondary">'.$text.'</a></p> ';
		} else {
			return '<p class="text-danger">HTML_Helper.setHrefText - На входе недостаточно данных!</p>';
		}
	}

	/**
     * Creates HREF as button.
     *
     * @return string
     */
	public static function setHrefButton($href, $class, $text)
	{
		if (!empty($href) && !empty($class) && !empty($text)) {
			return '<a href="/'.BEHAVIOR.'/'.$href.'" class="'.$class.'">'.$text.'</a> ';
		} else {
			return '<p class="text-danger">HTML_Helper.setHrefButton - На входе недостаточно данных!</p>';
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
