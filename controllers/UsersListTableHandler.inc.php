<?php

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
        $templateMgr->assign("userRoles", $roles);//Necesario para el botón usuarios

        
        
        $data = $this->getAllUsers();
        $templateMgr->assign("usersTable", $this->listUsers($data));

        $currentPage = isset($_GET["page"]) ? $_GET["page"] : 1;
        
        #$totalPages = $this->getSearchTotalPages($search);
        #$templateMgr->assign("paginationControl", $this->paginationControl($currentPage,$totalPages));
        
        return $templateMgr->display($plugin->getTemplateResource("usersListTable.tpl"));
    }
    
    public function getAllUsers(){
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $result = $usersListTableDAO->getAllUsers();

        return $result;
    }
    public function listUsers($data){
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

    /*public function paginationControl($currentPage, $totalPages)
    {
        $paginationControl = '<ul class="pagination">';

        if ($currentPage > 1) {
            $paginationControl .= '<li><a href="?page=' . (1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;&laquo;</span></a></li>';
            $paginationControl .= '<li><a href="?page=' . ($currentPage - 1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        for ($i = ($currentPage - 5); $i <= $totalPages; $i++) {
            if ($i > 0 && ($i < ($currentPage + 10))) {
                if ($currentPage == $i) {
                    $paginationControl .= '<li class="active"><a href="#">' . ($i) . '<span class="sr-only">(current)</span></a></li>';
                } else {
                    $paginationControl .= '<li><a href="?page=' . ($i) . '">' . ($i) . "</a></li>";
                }
            }
        }

        if ($currentPage < $totalPages) {
            $paginationControl .= '<li><a href="?page=' . ($currentPage + 1) . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
            $paginationControl .= '<li><a href="?page=' . serialize($totalPages) . '" aria-label="Next"><span aria-hidden="true">&raquo;&raquo;</span></a>';
        }

        $paginationControl .= "</ul>";

        return $paginationControl;
    }

    public function getSearchTotalPages()
    {
        //$search = isset($_POST["search"]) ? $_POST["search"] : '';

        $summaryHeaderDAO = DAORegistry::getDAO("SummaryHeaderDAO");
        $totalPages = $summaryHeaderDAO->getBySearchTotalPages($search);

        //error_log($totalPages);

        return $totalPages;
    }
*/
    public function translateRolesIdToText($roles){
        $arrayRoles= explode(",",$roles);
        $convertedRoles="";
        foreach ($arrayRoles as &$element) {
            switch($element){
                case "1":
                    $convertedRoles.="Administrador del sitio,";
                break;
                case "16":
                    $convertedRoles.="Director,";
                break;
                case "17":
                    $convertedRoles.="Sub editor,";
                break;
                case "4096":
                    $convertedRoles.="Evaluador,";
                break;
                case "4097":
                    $convertedRoles.="Asistente,";
                break;
                case "65536":
                    $convertedRoles.="Autor,";
                break;
                case "1048576":
                    $convertedRoles.="Lector,";
                break;
                case "2097152":
                    $convertedRoles.="Gestor de suscripciones,";
                break;
            }
        }

        $lastOccurrence = strrpos($convertedRoles, ',');
        if ($lastOccurrence !== false) {
            $convertedRoles = substr_replace($convertedRoles, '', $lastOccurrence, 1);          
        }
        return $convertedRoles;
    }
}