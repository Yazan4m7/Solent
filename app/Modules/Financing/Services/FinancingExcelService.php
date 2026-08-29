<?php

namespace App\Modules\Financing\Services;

class FinancingExcelService
{
    /**
     * Dependency-free Excel-compatible .xls export.
     * This keeps the module usable even if Maatwebsite/Excel is not installed.
     */
    public function download($filename, array $headers, array $rows)
    {
        $html = '<html><head><meta charset="UTF-8"></head><body><table border="1"><thead><tr>';

        foreach ($headers as $header) {
            $html .= '<th>' . e($header) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . e($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
