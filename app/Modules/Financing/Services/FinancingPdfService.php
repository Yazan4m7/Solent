<?php

namespace App\Modules\Financing\Services;

class FinancingPdfService
{
    public function download($view, array $data, $filename, $paper = 'a4', $orientation = 'portrait')
    {
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdfClass = '\Barryvdh\DomPDF\Facade\Pdf';

            return $pdfClass::loadView($view, $data)
                ->setPaper($paper, $orientation)
                ->download($filename);
        }

        if (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView($view, $data);
            $pdf->setPaper($paper, $orientation);

            return $pdf->download($filename);
        }

        abort(500, 'No supported PDF engine was detected. Connect this service to SIGMA\'s existing PDF library.');
    }
}
