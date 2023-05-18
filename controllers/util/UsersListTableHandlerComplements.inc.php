<?php

class UsersListTableHandlerComplements
{
    public function __construct()
    {
    }

    public function setRolesAndCountries()
    {
        $optionsCountry = array(
            '' => 'Todos',
            'CO' => 'Colombia',
            'VE' => 'Venezuela',
            'EC' => 'Ecuador',
            'PE' => 'Perú',
            'BR' => 'Brazil',
            'BO' => 'Bolivia',
            'PY' => 'Paraguay',
            'CL' => 'Chile',
            'UR' => 'Uruguay',
            'AR' => 'Argentina',
            'MX' => 'México',
            'CR' => 'Costa Rica',
            'RD' => 'Republica Dominicana',
            'PA' => 'Panamá',
            'US' => 'Estados Unidos',
            'ES' => 'España',
            'CA' => 'Canadá',
            'IT' => 'Italia',
            'CU' => 'Cuba',
            'AF' => 'Afganistan',
            'HA' => 'Honduras'
        );
        $optionsRoles = array(
            '' => 'Todos',
            '1' => 'admin del sitio',
            '2' => 'Gestor/a de revista',
            '3' => 'Editor/a de la revista',
            '4' => 'Coordinador/a de producción',
            '5' => 'Editor/a de sección',
            '6' => 'Editor/a invitado/a',
            '7' => 'Corrector/a de estilo',
            '8' => 'Diseñador/a',
            '9' => 'Coordinador/a de financiación',
            '10' => 'Documentalista',
            '11' => 'Maquetador/a',
            '12' => 'Coordinador/a de marketing y ventas',
            '13' => 'Corrector/a de pruebas',
            '14' => 'Autor/a',
            '15' => 'Traductor/a',
            '16' => 'Revisor/a externo',
            '17' => 'Lector/a',
            '18' => 'Gestor/a de suscripción'
        );
        return array($optionsCountry, $optionsRoles);
    }
    public function translateRolesIdToText($roles)
    {
        $arrayRoles = explode(",", $roles);
        $convertedRoles = "";
        foreach ($arrayRoles as &$element) {
            switch ($element) {
                case "1":
                    $convertedRoles .= "Administrador del sitio,";
                    break;
                case "2":
                    $convertedRoles .= "Gestor,";
                    break;
                case "3":
                    $convertedRoles .= "Editor,";
                    break;
                case "4":
                    $convertedRoles .= "Coordinador,";
                    break;
                case "5":
                    $convertedRoles .= "Editor de seccion,";
                    break;
                case "6":
                    $convertedRoles .= "Editor invitado,";
                    break;
                case "7":
                    $convertedRoles .= "Corrector de estilo,";
                    break;
                case "8":
                    $convertedRoles .= "Diseñador/a,";
                    break;
                case "9":
                    $convertedRoles .= "Coordinador/a de financiación,";
                    break;
                case "10":
                    $convertedRoles .= "Documentalista,";
                    break;
                case "11":
                    $convertedRoles .= "Maquetador/a,";
                    break;
                case "12":
                    $convertedRoles .= "Coordinador/a de marketing y ventas,";
                    break;
                case "13":
                    $convertedRoles .= "Corrector/a de pruebas,";
                    break;
                case "14":
                    $convertedRoles .= "Autor/a,";
                    break;
                case "15":
                    $convertedRoles .= "Traductor/a,";
                    break;
                case "16":
                    $convertedRoles .= "Revisor/a externo,";
                    break;
                case "17":
                    $convertedRoles .= "Lector/a,";
                    break;
                case "18":
                    $convertedRoles .= "Gestor/a de suscripción,";
                    break;
                default:
                    $convertedRoles .= "Sin rol,";
            }
        }
        $lastOccurrence = strrpos($convertedRoles, ',');
        if ($lastOccurrence !== false) {
            $convertedRoles = substr_replace($convertedRoles, '', $lastOccurrence, 1);
        }
        return $convertedRoles;
    }
    public function completeCountryName($country)
    {
        switch ($country) {
            case "CO":
               return '<p>Colombia</p>';
            case "VE":
                return "<p>Venezuela</p>";
            case "EC":
                return "<p>Ecuador</p>";
            case "PE":
                return "<p>Peru</p>";
            case "BR":
                return "<p>Brazil</p>";
            case "BO":
                return "<p>Bolivia</p>";
            case "PY":
                return "<p>Paraguay</p>";
            case "CL":
                return "<p>Chile</p>";
            case "UR":
                return "<p>Uruguay</p>";
            case "AR":
                return "<p>Argentina</p>";
            case "MX":
                return "<p>Mexico</p>";
            case "CR":
                return "<p>CostaRica</p>";
            case "RD":
                return "<p>Republica Dominicana</p>";
            case "PA":
                return "<p>Panama</p>";
            case "US":
                return "<p>EstadosUnidos</p>";
            case "ES":
                return "<p>España</p>";
            case "CA":
                return "<p>Canada</p>";
            case "IT":
                return "<p>Italia</p>";
            case "CU":
                return "<p>Cuba</p>";
            case "AF":
                return "<p>Afganistan</p>";
            case "HA":
                return "<p>Honduras</p>";
            
            default:
                return "<p>sinPais</p>";
        }
    }
}
