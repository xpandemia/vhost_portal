<?php

namespace tinyframe\core\helpers;

use mikehaertl\pdftk\Pdf;

class PDF_Helper
{
	/*
		PDF processing
	*/

	/**
     * Creates PDF.
     *
     * @return nothing
     */
	public static function create($data, $layout, $output)
	{
		$pdf_original = ROOT_DIR.'/files/pdf/'.$layout.'.pdf';
		$pdf_filename = $output.'.pdf';
		$pdf = new Pdf($pdf_original);
		$pdf->fillForm($data)
			->needAppearances()
		    ->saveAs($pdf_filename);
		$pdf->send($pdf_filename, true);
		unlink($pdf_filename);
	}
}
