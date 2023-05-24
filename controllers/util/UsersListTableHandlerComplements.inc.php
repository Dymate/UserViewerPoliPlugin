<?php
/*
 *
 * Módolo de Gestión para la búsqueda de usuarios
 * Dylan Mateo Llano Jaramillo & Juan José Restrepo Correa
 * Politécnico Colombiano Jaime Isaza Cadavid
 * Medellín-Colombia Mayo de 2023
 *
 */
class UsersListTableHandlerComplements
{
    public function __construct()
    {
    }

    public function setRolesAndCountries()
    {   //inicializa las variables de los dropdown
        $optionsCountry = array(
            '' => 'Todos',
            'AF' => 'Afganistán',
            'AM' => 'Armenia',
            'AQ' => 'Antártida',
            'AR' => 'Argentina',
            'AT' => 'Austria',
            'AU' => 'Australia',
            'BA' => 'Bosnia y Herzegovina',
            'BB' => 'Barbados',
            'BE' => 'Bélgica',
            'BF' => 'Burkina Faso',
            'BJ' => 'Benín',
            'BM' => 'Bermudas',
            'BN' => 'Brunéi',
            'BO' => 'Bolivia',
            'BR' => 'Brasil',
            'CA' => 'Canadá',
            'CD' => 'República Democrática del Congo',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'DE' => 'Alemania',
            'DO' => 'República Dominicana',
            'EC' => 'Ecuador',
            'EE' => 'Estonia',
            'EG' => 'Egipto',
            'ES' => 'España',
            'GA' => 'Gabón',
            'GF' => 'Guayana Francesa',
            'GS' => 'Islas Georgia del Sur y Sandwich del Sur',
            'GT' => 'Guatemala',
            'HN' => 'Honduras',
            'HR' => 'Croacia',
            'HU' => 'Hungría',
            'ID' => 'Indonesia',
            'IE' => 'Irlanda',
            'IN' => 'India',
            'IT' => 'Italia',
            'JO' => 'Jordania',
            'KR' => 'Corea del Sur',
            'LA' => 'Laos',
            'LT' => 'Lituania',
            'MA' => 'Marruecos',
            'MF' => 'San Martín (parte francesa)',
            'ML' => 'Malí',
            'MP' => 'Islas Marianas del Norte',
            'MW' => 'Malaui',
            'MX' => 'México',
            'NR' => 'Nauru',
            'PE' => 'Perú',
            'PG' => 'Papúa Nueva Guinea',
            'PR' => 'Puerto Rico',
            'PT' => 'Portugal',
            'SO' => 'Somalia',
            'SV' => 'El Salvador',
            'UA' => 'Ucrania',
            'UG' => 'Uganda',
            'US' => 'Estados Unidos',
            'UY' => 'Uruguay',
            'VE' => 'Venezuela',
            'ZM' => 'Zambia'
        );
        $optionsRoles = array(
            '' => 'Todos',
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
    //convierte los roles numericos de la base de datos en su correspondiente a texto
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
    //convierte las abreviaciones de cada país en su nombre completo
    public function completeCountryName($country)
    {
        switch ($country) {
            case "AF":
                return '<p>Afganistán</p>';
            case "AM":
                return '<p>Armenia</p>';
            case "AQ":
                return '<p>Antártida</p>';
            case "AR":
                return '<p>Argentina</p>';
            case "AT":
                return '<p>Austria</p>';
            case "AU":
                return '<p>Australia</p>';
            case "BA":
                return '<p>Bosnia y Herzegovina</p>';
            case "BB":
                return '<p>Barbados</p>';
            case "BE":
                return '<p>Bélgica</p>';
            case "BF":
                return '<p>Burkina Faso</p>';
            case "BJ":
                return '<p>Benín</p>';
            case "BM":
                return '<p>Bermudas</p>';
            case "BN":
                return '<p>Brunéi</p>';
            case "BO":
                return '<p>Bolivia</p>';
            case "BR":
                return '<p>Brasil</p>';
            case "CA":
                return '<p>Canadá</p>';
            case "CD":
                return '<p>República Democrática del Congo</p>';
            case "CL":
                return '<p>Chile</p>';
            case "CO":
                return '<p>Colombia</p>';
            case "CR":
                return '<p>Costa Rica</p>';
            case "CU":
                return '<p>Cuba</p>';
            case "DE":
                return '<p>Alemania</p>';
            case "DO":
                return '<p>República Dominicana</p>';
            case "EC":
                return '<p>Ecuador</p>';
            case "EE":
                return '<p>Estonia</p>';
            case "EG":
                return '<p>Egipto</p>';
            case "ES":
                return '<p>España</p>';
            case "GA":
                return '<p>Gabón</p>';
            case "GF":
                return '<p>Guayana Francesa</p>';
            case "GS":
                return '<p>Islas Georgia del Sur y Sandwich del Sur</p>';
            case "GT":
                return '<p>Guatemala</p>';
            case "HN":
                return '<p>Honduras</p>';
            case "HR":
                return '<p>Croacia</p>';
            case "HU":
                return '<p>Hungría</p>';
            case "ID":
                return '<p>Indonesia</p>';
            case "IE":
                return '<p>Irlanda</p>';
            case "IN":
                return '<p>India</p>';
            case "IT":
                return '<p>Italia</p>';
            case "JO":
                return '<p>Jordania</p>';
            case "KR":
                return '<p>Corea del Sur</p>';
            case "LA":
                return '<p>Laos</p>';
            case "LT":
                return '<p>Lituania</p>';
            case "MA":
                return '<p>Marruecos</p>';
            case "MF":
                return '<p>San Martín (parte francesa)</p>';
            case "ML":
                return '<p>Malí</p>';
            case "MP":
                return '<p>Islas Marianas del Norte</p>';
            case "MW":
                return '<p>Malaui</p>';
            case "MX":
                return '<p>México</p>';
            case "NR":
                return '<p>Nauru</p>';
            case "PE":
                return '<p>Perú</p>';
            case "PG":
                return '<p>Papúa Nueva Guinea</p>';
            case "PR":
                return '<p>Puerto Rico</p>';
            case "PT":
                return '<p>Portugal</p>';
            case "SO":
                return '<p>Somalia</p>';
            case "SV":
                return '<p>El Salvador</p>';
            case "UA":
                return '<p>Ucrania</p>';
            case "UG":
                return '<p>Uganda</p>';
            case "US":
                return '<p>Estados Unidos</p>';
            case "UY":
                return '<p>Uruguay</p>';
            case "VE":
                return '<p>Venezuela</p>';
            case "ZM":
                return '<p>Zambia</p>';
            default:
                return 'Sin país';
        }
    }
}
