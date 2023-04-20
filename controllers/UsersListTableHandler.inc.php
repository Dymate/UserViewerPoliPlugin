<?php

use PhpOption\None;

import("classes.handler.Handler");


class UsersListTableHandler extends Handler
{

    public function index($args, $request)
    {
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_USER);
        $roles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
        if (count(array_intersect(array(ROLE_ID_SITE_ADMIN), $roles)) == 0) {
            header('HTTP/1.0 403 Forbidden');
            return print('<h2>Acceso denegado</h2>No tienes permitido ingresar a esta sección.');
        }

        $plugin = PluginRegistry::getPlugin("generic", "userviewerpoliplugin");
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign("userRoles", $roles); //Necesario para el botón usuarios
        $currentPage = isset($_GET["page"]) ? $_GET["page"] : 1;
        $data = $this->getAllUsers($currentPage);
        $templateMgr->assign("usersTable", $this->listUsers($data));

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
        $selectedCountryValue = "";
        $selectedRolesValue = "";
        $templateMgr->assign("optionsCountry", $optionsCountry);
        $templateMgr->assign("optionsRoles", $optionsRoles);
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $lastName = isset($_POST['lastname']) ? $_POST['lastname'] : null;
        $username = isset($_POST['username']) ? $_POST['username'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $university=isset($_POST['university'])? $_POST['university']:null;
        $user_id=isset($_POST['user_id'])? $_POST['user_id']:null;
        $country = isset($_POST['country']) ? $_POST['country'] : null;
        $templateMgr->assign("selectedCountryValue", $country);
        $userRoles = isset($_POST['roles']) ? $_POST['roles'] : null;
        $templateMgr->assign("selectedRolesValue", $userRoles);

        if (($name or $lastName or $username or $email or $country or $userRoles) != null) {
            $data = $this->generateSearchFilter($name, $lastName, $username, $email, $country, $userRoles);
            $templateMgr->assign("usersTable", $this->listUsers($data));
        }
       if(($university and $user_id)!=null){
           $this->updateUniversity($user_id,$university);
           $url = $_SERVER['REQUEST_URI'];
           header("Location: $url");

        }
        $totalPages = $this->getTotalPages();
        $templateMgr->assign("paginationControl", $this->paginationControl($currentPage, $totalPages));

        return $templateMgr->display($plugin->getTemplateResource("usersListTable.tpl"));
    }

    public function generateSearchFilter($name, $lastName, $username, $email, $country, $userRoles)
    {

        $sql = "SELECT	search.user_id,search.firstName, search.lastName, search.username, search.email,search.country, search.roles
        FROM (  
            SELECT u.user_id,
                   MAX(CASE WHEN us.setting_name = 'givenName' THEN us.setting_value END) AS firstName,
                   MAX(CASE WHEN us.setting_name = 'familyName' THEN us.setting_value END) AS lastName,
                   u.username,
                   u.email,
                   u.country,
                   GROUP_CONCAT(DISTINCT uug.user_group_id SEPARATOR ',') AS roles
            FROM users u 
            LEFT JOIN user_settings us ON u.user_id = us.user_id
            LEFT JOIN user_user_groups uug ON u.user_id = uug.user_id
            GROUP BY u.user_id) search
        WHERE 1=1 ";

        if ($name) {
            $sql .= "AND search.firstName LIKE '%$name%'";
        }
        if ($lastName) {
            $sql .= "AND search.lastName LIKE '%$lastName%'";
        }
        if ($username) {
            $sql .= "AND search.username LIKE '%$username%'";
        }
        if ($email) {
            $sql .= "AND search.email LIKE '%$email%'";
        }
        if ($country) {
            $sql .= "AND search.country LIKE '%$country%'";
        }
        if ($userRoles) {
            if ($userRoles == 1) {
                $sql .= "AND search.roles LIKE '%1,%'";
            } else {
                $sql .= "AND search.roles LIKE '%$userRoles%'";
            }
        }

        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $result = $usersListTableDAO->searchUsers($sql);

        return $result;
    }
    public function getAllUsers($currentPage)
    {
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $result = $usersListTableDAO->getAllUsers($currentPage);

        return $result;
    }
    public function listUsers($data)
    {
        $table = '';
        foreach ($data as $row) {
            $table .= '
                <tr>
                  <td>   
                  <input type="checkbox" name="Select" value="1"></td>
                  <td>' . $row->getFirstName() . '</td>
                  <td>' . $row->getLastName() . '</td>
                  <td>' . $row->getCountry() . '</td>
                  <td>' . $this->translateRolesIdToText($row->getRoles()) . '</td>
                  
                  <td>
                      <button class="btn btn-warning"type="button" data-toggle="modal" data-target="#myModal' . $row->getUserId() . '">
                          <i class="glyphicon glyphicon-eye-open" style="color: black"></i>
                          
                      </button>&nbsp;
                  </td>
                </tr>';
            $table = $this->generateModalWindow($row, $table);
        }
        return $table;
    }
    public function generateModalWindow($user, $table)
    {
        $table .= '
<div class="modal fade" id="myModal' . $user->getUserId() . '" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <!-- Encabezado de la ventana modal -->
            <div class="modal-header" style="height:70px">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="h1" style="">Información del usuario</div>
                        </div>

                        <div class="col-sm-3"><button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cuerpo de la ventana modal -->
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="h4"> ID:' . $user->getUserId() . '</div>
                            <div class="h3">' . $user->getFirstName() . ' ' . $user->getLastName() . '</div>
                           
                        </div>
                        <div class="col-sm-6">
                            <div><b>Usuario: </b>' . $user->getUserName() . '</div>
                            <div><b>Email: </b>' . $user->getEmail() . '</div>
                            <div><b>Nacionalidad: </b>' . $user->getCountry() . '</div>
                            <div><b>Universidad: </b> ' . $user->getUniversity() . ' <button class="text-success" style="border:none; background:none" type="button" data-toggle="modal" data-target="#mySecondModal' . $user->getUserId() . '">
                            <i class="text-success glyphicon glyphicon-pencil" ></i>
                        </button>&nbsp;</div>
                            <div><b>Grado universitario: </b> Ing Informatico  </div>
                            <div><b>Roles: </b>' . $this->translateRolesIdToText($user->getRoles()) . '</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="h3 text-muted">Biografía</div>
                    </div>
                    <div class="col-sm-6">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat odit iste
                        maiores nobis officiis perferendis, blanditiis eligendi, hic est vitae error molestiae
                        consectetur dignissimos ratione voluptatibus vel sint eveniet quos.</div>
                </div>
                <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
  
  </div>
</div>
       
<div class="modal fade" id="mySecondModal' . $user->getUserId() . '" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" id="exampleModalToggleLabel2">Añadir universidad al usuario</h1>
        
      </div>
      <div class="modal-body">
      <form  method="POST">
      
      <input type="hidden" name="user_id" value="'.$user->getUserId().'" style="display:none;">
      <div><b>Universidad: </b><input type="text" name="university" value="' . $user->getUniversity() . '"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" type=submit>Actualizar</button>
        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

        </form>
      </div>
    </div>
  </div>
</div>
';

        if (strpos($user->getRoles(), "2") !== false or strpos($user->getRoles(), "3") !== false) {
        }
        if (strpos($user->getRoles(), "14") !== false) {
        }
        $table .= '
                  </div>
                </div>
            </div>
        </div>';

        return $table;
    }
    public function paginationControl($currentPage, $totalPages)
    {
        $paginationControl = '<ul class="pagination">';

        if ($currentPage > 1) {
            $paginationControl .= '<li><a href="?page=' . (1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;&laquo;</span></a></li>';
            $paginationControl .= '<li><a href="?page=' . ($currentPage - 1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        for ($i = ($currentPage - 5); $i <= $totalPages; $i++) {
            if ($i > 0 && ($i < ($currentPage + 10))) {
                if ($currentPage == $i) {
                    $paginationControl .= '<li class="active"><a href="">' . ($i) . '<span class="sr-only">(current)</span></a></li>';
                } else {
                    $paginationControl .= '<li><a href="?page=' . ($i) . '">' . ($i) . "</a></li>";
                }
            }
        }

        if ($currentPage < $totalPages) {
            $paginationControl .= '<li><a href="?page=' . ($currentPage + 1) . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
            $paginationControl .= '<li><a href="?page=' . $totalPages . '" aria-label="Next"><span aria-hidden="true">&raquo;&raquo;</span></a>';
        }

        $paginationControl .= "</ul>";

        return $paginationControl;
    }

    public function getTotalPages()
    {
        //$search = isset($_POST["search"]) ? $_POST["search"] : '';

        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $totalPages = ceil(($usersListTableDAO->countUsers()) / 10);
        return $totalPages;
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

    public function updateUniversity($user_id,$university){
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $rowsAffected=$usersListTableDAO->updateUniversity($user_id,$university);
    }
}
