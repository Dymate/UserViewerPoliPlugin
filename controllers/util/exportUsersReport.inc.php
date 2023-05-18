<?php
require_once(dirname(__FILE__) . '/../../lib/PhpSpreadsheet/vendor/autoload.php');
import("plugins.generic.userViewerPoliPlugin.controllers.util.UsersListTableHandlerComplements");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class exportUsersReport
{

    public function exportUsers($usersToExport)
    {   
     
    
        $usersToExport = str_replace(['"', "[", "]"], "", $usersToExport);
        $arrayUsersId = explode(",", $usersToExport);
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $UserListComplements = new UsersListTableHandlerComplements();
       
        // Crear instancia de Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Crear hoja de cálculo
        $sheet = $spreadsheet->getActiveSheet();

        // Agregar datos a la hoja de cálculo
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Apellido');
        $sheet->setCellValue('D1', 'Nacionalidad');
        $sheet->setCellValue('E1', 'Universidad');
        $sheet->setCellValue('F1', 'Grado Academico');
        $sheet->setCellValue('G1', 'Biografía');
        $sheet->setCellValue('H1', 'Nombre de usuario');
        $sheet->setCellValue('I1', 'Correo');
        $sheet->setCellValue('J1', 'Roles');
        $index = 2;
        // Obtener datos de la base de datos y agregarlos a la hoja de cálculo
        foreach ($arrayUsersId as $userId) {
            $user = $usersListTableDAO->getUserById($userId);
            if ($user !== null) {
            $sheet->setCellValue('A' . $index, $user->getUserId());
            $sheet->setCellValue('B' . $index, $user->getFirstName());
            $sheet->setCellValue('C' . $index, $user->getLastName());
            $sheet->setCellValue('D' . $index, $user->getCountry());
            $sheet->setCellValue('E' . $index,  $user->getUniversity());
            $sheet->setCellValue('F' . $index, $user->getAcademicDegree());
            $sheet->setCellValue('G' . $index,  $user->getBiography());
            $sheet->setCellValue('H' . $index,  $user->getUserName());
            $sheet->setCellValue('I' . $index,  $user->getEmail());
            $roles = $UserListComplements->translateRolesIdToText($user->getRoles());
            $sheet->setCellValue('J' . $index, $roles);
            }
            $index++;
        }
        // Guardar archivo en una ruta temporal
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $temp_file = tempnam(sys_get_temp_dir(), 'reporte_');
        $writer->save($temp_file);

        // Descargar archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte.xlsx"');
        header('Cache-Control: max-age=0');
        readfile($temp_file);
        unlink($temp_file); // Eliminar archivo temporal
        exit();
    }
    public function exportAllUsers($usersResult){
        $UserListComplements = new UsersListTableHandlerComplements();
        
        // Crear instancia de Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Crear hoja de cálculo
        $sheet = $spreadsheet->getActiveSheet();

        // Agregar datos a la hoja de cálculo
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Apellido');
        $sheet->setCellValue('D1', 'Nacionalidad');
        $sheet->setCellValue('E1', 'Universidad');
        $sheet->setCellValue('F1', 'Grado Academico');
        $sheet->setCellValue('G1', 'Biografía');
        $sheet->setCellValue('H1', 'Nombre de usuario');
        $sheet->setCellValue('I1', 'Correo');
        $sheet->setCellValue('J1', 'Roles');
        $index = 2;
        // Obtener datos de la base de datos y agregarlos a la hoja de cálculo
        foreach ($usersResult as $user) {
            $sheet->setCellValue('A' . $index, $user->getUserId());
            $sheet->setCellValue('B' . $index, $user->getFirstName());
            $sheet->setCellValue('C' . $index, $user->getLastName());
            $sheet->setCellValue('D' . $index, $user->getCountry());
            $sheet->setCellValue('E' . $index,  $user->getUniversity());
            $sheet->setCellValue('F' . $index, $user->getAcademicDegree());
            $sheet->setCellValue('G' . $index,  $user->getBiography());
            $sheet->setCellValue('H' . $index,  $user->getUserName());
            $sheet->setCellValue('I' . $index,  $user->getEmail());
            $roles = $UserListComplements->translateRolesIdToText($user->getRoles());
            $sheet->setCellValue('J' . $index, $roles);
            $index++;
        } 
         // Guardar archivo en una ruta temporal
         $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
         $temp_file = tempnam(sys_get_temp_dir(), 'reporte_');
         $writer->save($temp_file);
 
         // Descargar archivo
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="reporte.xlsx"');
         header('Cache-Control: max-age=0');
         readfile($temp_file);
         unlink($temp_file); // Eliminar archivo temporal
         exit();
    }
}
