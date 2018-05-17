<?php

namespace tinyframe\core\helpers;

use mikehaertl\pdftk\Pdf;

class PDF_Helper
{
	/*
		PDF processing
	*/

	const PDF_PASSWORD = 'nopainnotrain';

	/**
     * Creates PDF.
     *
     * @return nothing
     */
	public static function create($data, $layout, $output)
	{
		$pdf_original = ROOT_DIR.'/files/pdf/'.$layout.'.pdf';
		$pdf_filename = ROOT_DIR.'/files/temp/'.$output.'.pdf';
		$pdf = new Pdf($pdf_original);
		$pdf->fillForm($data)
			->needAppearances()
			->setPassword(self::PDF_PASSWORD)
		    ->saveAs($pdf_filename);
		$pdf->send($pdf_filename, true);
		unlink($pdf_filename);
	}
}
