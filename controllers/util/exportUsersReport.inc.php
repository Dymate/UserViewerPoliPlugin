<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;class exportUsersReport
{

    public function exportUsers()
    {
        $spreadsheet = new Spreadsheet();

        // Selecciona la hoja de cálculo activa
        $sheet = $spreadsheet->getActiveSheet();
        
        // Agrega algunos datos a la hoja de cálculo
        $sheet->setCellValue('A1', 'Nombre');
        $sheet->setCellValue('B1', 'Apellido');
        $sheet->setCellValue('C1', 'Edad');
        $sheet->setCellValue('A2', 'Juan');
        $sheet->setCellValue('B2', 'Pérez');
        $sheet->setCellValue('C2', '25');
        $sheet->setCellValue('A3', 'María');
        $sheet->setCellValue('B3', 'García');
        $sheet->setCellValue('C3', '30');
        
        // Crea un objeto Writer para guardar el archivo Excel
        $writer = new Xlsx($spreadsheet);
        
        // Configura la descarga del archivo Excel en el navegador
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="datos.csv"');
        header('Cache-Control: max-age=0');
        
        // Guarda el archivo Excel en el buffer de salida y lo envía al navegador
        $writer->save('php://output');
    }
}
