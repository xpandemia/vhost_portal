<?php

namespace tinyframe\core\helpers;

define("CAPTCHA_LEN", 6);

class Captcha_Helper
{
	/*
		Captcha processing
	*/

	/**
     * Creates URL with BASEPATH.
     *
     * @return string
     */
     public function create()
	{
		$captchastring = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
		// gets first CAPTCHA_LEN sybmols after str_shuffle
		$captchastring = substr(str_shuffle($captchastring), 0, CAPTCHA_LEN);
		// saves CAPTCHA code
		if (isset($_SESSION)) {
			$_SESSION['captcha'] = $captchastring;
		}

		// generates CAPTCHA image
		$image = imagecreatefrompng(ROOT_DIR.'/images/captcha.png');
		$colour = imagecolorallocate($image, 200, 240, 240);
		$font = ROOT_DIR.'/fonts/oswald.ttf';
		$rotate = rand(-5, 5);
		imagettftext($image, 18, $rotate, 30, 30, $colour, $font, $captchastring);

		// saves CAPTCHA image
		imagepng($image, ROOT_DIR.'/images/temp/captcha/captcha_'.session_id().'.png');
		// destroys CAPTCHA image instance
		imagedestroy($image);
	}
}
