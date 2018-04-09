<?php

namespace tinyframe\core\helpers;

class Calc_Helper
{
	/*
		Calculation processing
	*/

	/**
     * Gets age between date and today.
     *
     * @return int
     */
	public static function getAge($dt, $format)
	{
		$dt = date($format, strtotime($dt));
		$today = date($format);
		$diff = date_diff(date_create($dt), date_create($today));
		return (int) $diff->format('%y');
	}
}
