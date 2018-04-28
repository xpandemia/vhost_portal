<?php

namespace tinyframe\core\helpers;

class PDF_Helper
{
	/*
		PDF processing
	*/

	/**
     * Creates URL with BASEPATH.
     *
     * @return string
     */
	public static function create($data, $names, $layout, $output, $hidden = [], $readony = [])
	{
		$pdf = new \pdftk_php();
		// text fields, combo boxes and list boxes
		$fdf_data_strings = $data;
		// check boxes and radio buttons
		$fdf_data_names = $names;
		// hidden fields
		$fields_hidden = $hidden;
		// readonly fields
		$fields_readonly = $readony;
		// PDF layout-file
		$pdf_original = ROOT_DIR.'/files/pdf/'.$layout.'.pdf';
		// PDF output-file
		$pdf_filename = $output.'.pdf';
		// creation
		$pdf->make_pdf($fdf_data_strings, $fdf_data_names, $fields_hidden, $fields_readonly, $pdf_original, $pdf_filename);
	}
}
