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
    public static function create( $data, $layout, $output, $debug = FALSE)
    {
        foreach($data as $key => $row) {
            $data[$key] = htmlspecialchars_decode($row);
        }
        
        if(isset($data["places"])){
            $rows = explode(';', $data["places"]);
        }
        
        $pdf_original = ROOT_DIR.'/files/pdf/'.$layout.'.pdf';
        
        if($debug) {
            echo '<pre>';
            var_dump([$pdf_original, file_exists($pdf_original)]);
            echo '</pre>';
        }
        
        $pdf_filename = $output.'.pdf';
        $pdf          = new Pdf($pdf_original);
        
		$pdf->fillForm($data, 'UTF-8', FALSE)
            //->flatten()
            ->needAppearances()
            ->saveAs($pdf_filename);
		
		$pdf->send($pdf_filename, TRUE);
		
		unlink($pdf_filename);
	}
}
