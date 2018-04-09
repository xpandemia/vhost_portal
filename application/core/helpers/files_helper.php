<?php

namespace tinyframe\core\helpers;

class Files_Helper
{
	/*
		Files processing
	*/

	/**
     * Gets file extension.
     *
     * @return string
     */
	public static function getExtension($file_name)
	{
		$tmp = explode('.', $file_name);
		$file_ext = end($tmp);
		return $file_ext;
	}

	/**
     * Gets file size.
     *
     * @return int
     */
	public static function getSize($file_size, $size)
	{
		switch ($size) {
			case 'B':
				return $file_size;
			case 'kB':
				return round($file_size / 1024, 3);
			case 'MB':
				return round($file_size / 1024 / 1024, 3);
			case 'GB':
				return round($file_size / 1024 / 1024 / 1024, 3);
			case 'TB':
				return round($file_size / 1024 / 1024 / 1024 / 1024, 3);
			case 'PB':
				return round($file_size / 1024 / 1024 / 1024 / 1024 / 1024, 3);
			case 'EB':
				return round($file_size / 1024 / 1024 / 1024 / 1024 / 1024 / 1024, 3);
			case 'ZB':
				return round($file_size / 1024 / 1024 / 1024 / 1024 / 1024 / 1024 / 1024, 3);
			case 'YB':
				return round($file_size / 1024 / 1024 / 1024 / 1024 / 1024 / 1024 / 1024 / 1024, 3);
			default:
				throw new \InvalidArgumentException('Неизвестная размерность файла!');
		}
	}
}
