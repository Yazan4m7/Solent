<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class DocumentationController extends Controller
{
    /**
     * Generate a comprehensive PDF documentation of the software
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDF()
    {
        // Create PDF instance
        $pdf = PDF::loadView('documentation.features');
        
        // Set PDF options
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        // Return the PDF for download
        return $pdf->download('lab-management-pro-features.pdf');
    }
}
