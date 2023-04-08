<?php

use PhpOption\None;

import("classes.handler.Handler");


class UsersListTableHandler extends Handler
{

    public function index($args, $request)
    {
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_USER);
        $roles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
        if (count(array_intersect(array(ROLE_ID_SITE_ADMIN, ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR), $roles)) == 0) {
            header('HTTP/1.0 403 Forbidden');
            return print('<h2>Acceso denegado</h2>No tienes permitido ingresar a esta sección.');
        }
 
        $plugin = PluginRegistry::getPlugin("generic", "userviewerpoliplugin");
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign("userRoles", $roles); //Necesario para el botón usuarios
        $currentPage = isset($_GET["page"]) ? $_GET["page"] : 1;
        $data = $this->getAllUsers($currentPage);
        $templateMgr->assign("usersTable", $this->listUsers($data));
        
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $lastName = isset($_POST['lastname']) ? $_POST['lastname'] : null;
        $username = isset($_POST['username']) ? $_POST['username'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $country = isset($_POST['country']) ? $_POST['country'] : null;
        $userRoles = isset($_POST['roles']) ? $_POST['roles'] : null;
        
        if (($name or $lastName or $username or $email or $country or $userRoles) != null) {
            $data = $this->generateSearchFilter($name, $lastName, $username, $email, $country, $userRoles);
            $templateMgr->assign("usersTable", $this->listUsers($data));
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
                   GROUP_CONCAT(DISTINCT r.role_id SEPARATOR ',') AS roles
            FROM users u 
            LEFT JOIN user_settings us ON u.user_id = us.user_id
            LEFT JOIN roles r ON u.user_id = r.user_id
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
            $sql .= "AND search.roles LIKE '%$userRoles%'";
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
                  <td>' . $row->getUserName() . '</td>
                  <td>' . $row->getEmail() . '</td>
                  <td>' . $row->getCountry() . '</td>
                  <td>' . $this->translateRolesIdToText($row->getRoles()) . '</td>
                  

                  <td>
                      <a title="Mas detalles" href="./userspoliplugin-view?id=' . $row->getUserId() . '">
                          <i class="glyphicon glyphicon-plus"></i>
                      </a>&nbsp;
                  </td>
                </tr>';
        }
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
}