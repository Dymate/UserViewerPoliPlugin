<?php
/*
 *
 * Módolo de Gestión para la búsqueda de usuarios
 * Dylan Mateo Llano Jaramillo & Juan José Restrepo Correa
 * Politécnico Colombiano Jaime Isaza Cadavid
 * Medellín-Colombia Mayo de 2023
 *
 */

import("classes.handler.Handler");
import("plugins.generic.userViewerPoliPlugin.controllers.util.UsersListTableHandlerComplements");
import("plugins.generic.userViewerPoliPlugin.controllers.util.GenerateUsersTable");

class UsersListTableHandler extends Handler
{
    public function newUserListComplement()
    {
        return new UsersListTableHandlerComplements();
    }
    public function newGenerateUsersTable()
    {
        return new GenerateUsersTable();
    }
    public function index($args, $request)
    {
        AppLocale::requireComponents(LOCALE_COMPONENT_PKP_COMMON, LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_USER);
        $roles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
        if (count(array_intersect(array(ROLE_ID_SITE_ADMIN,ROLE_ID_MANAGER), $roles)) == 0) {
            header('HTTP/1.0 403 Forbidden');
            return print('<h2>Acceso denegado</h2>No tienes permitido ingresar a esta sección.');
        }
        //DECLARACION DE VARIABLES
        $context = $request->getContext();
        $contextId = $context ? $context->getId() : CONTEXT_ID_NONE;
        $userListComplements = $this->newUserListComplement();
        $generateUsersTable = $this->newGenerateUsersTable();
        $plugin = PluginRegistry::getPlugin("generic", "userviewerpoliplugin");
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->addJavaScript('usersListTable', $plugin->getPluginBaseUrl() . '/js/usersListTable.js');
        $templateMgr->addStyleSheet('usersListTable', $plugin->getPluginBaseUrl() . '/css/usersListTable.css');
        $templateMgr->assign("userRoles", $roles); //Necesario para el botón usuarios
        $currentPage = isset($_GET["page"]) ? $_GET["page"] : 1;
        $data = $this->getAllUsers($currentPage,$contextId);
        $templateMgr->assign("usersTable", $generateUsersTable->listUsers($data,null));
        $selectedCountryValue = "";
        $selectedRolesValue = "";
        list($optionsCountry, $optionsRoles) = $userListComplements->setRolesAndCountries();
        
        //RECEPCION DE VARIABLES DEL FRONT
        $name = isset($_GET['name']) ? $_GET['name'] : null;
        $lastName = isset($_GET['lastnm']) ? $_GET['lastnm'] : null;
        $university = isset($_POST['university']) ? $_POST['university'] : null;
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
        $newAcademicDegree = isset($_POST['newAcademicDegree']) ? $_POST['newAcademicDegree'] : null;
        $biography = isset($_POST['biography']) ? $_POST['biography'] : null;
        $country = isset($_GET['country']) ? $_GET['country'] : null;
        $userRoles = isset($_GET['roles']) ? $_GET['roles'] : null;
        $exportAll = isset($_POST['exportAll']) ? $_POST['exportAll'] : null;
        if (isset($_SESSION['selectedValues'])) {
            $needExport = isset($_POST['selectedValues']) ? $_SESSION['selectedValues'] . $_POST['selectedValues'] : null;
            $_SESSION['selectedValues'] = $needExport;
        } else {
            $needExport = isset($_POST['selectedValues']) ? $_POST['selectedValues'] : null;
            $_SESSION['selectedValues'] = $needExport;
        }
        
        if ($exportAll!=null){
            $this->exportMassiveUsers($name,$lastName,$country,$userRoles,$contextId);
            $url = $_SERVER['REQUEST_URI'];
            header("Location: $url");
        }

        //ASIGNACION DE VARIABLES DE LA TEMPLATE
        $templateMgr->assign("selectedCountryValue", $country);
        $templateMgr->assign("selectedRolesValue", $userRoles);
        $templateMgr->assign("optionsCountry", $optionsCountry);
        $templateMgr->assign("optionsRoles", $optionsRoles);
        //$selectedUsers=isset($_POST['selectedValues']) ? $_POST['selectedValues'] : null;

        //ejecución de métodos según la petición
        if($needExport != null) {
            $_SESSION['selectedValues']=""; //se reinicia la variable global para evitar bugs en la exportación
            $this->exportUsers($needExport,$contextId,);
        } 
        list($data, $countResult) = $this->generateSearchFilter($name, $lastName, $country, $userRoles, $currentPage,$contextId);
        if (isset($data)) {
            $templateMgr->assign("usersTable", $generateUsersTable->listUsers($data,$userRoles));
            $totalPages = ceil(($countResult) / 10);
            $templateMgr->assign("totalUsers",$countResult);
            
        } else {
            $totalPages = $this->getTotalPages($contextId);
            $templateMgr->assign("totalUsers","∼".$totalPages*10);
        }
        
        $this->generateDataFromChart($userRoles,$templateMgr,$contextId);
    
        if (($university and $user_id) != null) {
            $this->updateUniversity($user_id, $university);
            $url = $_SERVER['REQUEST_URI'];
            header("Location: $url");
        }
        if (($user_id and $newAcademicDegree) != null) {
            $this->updateAcademicDegree($user_id, $newAcademicDegree);
            $url = $_SERVER['REQUEST_URI'];
            header("Location: $url");
        }
        if (($user_id and $biography) != null) {
            $this->updateBiography($user_id, $biography);
            $url = $_SERVER['REQUEST_URI'];
            header("Location: $url");
        }
        //genera la paginación
        $templateMgr->assign("paginationControl", $this->paginationControl($currentPage, $totalPages));

        return $templateMgr->display($plugin->getTemplateResource("usersListTable.tpl"));
    }
    //Genera exportacion de múltiples usuarios
    public function exportMassiveUsers($name, $lastName, $country, $userRoles,$contextId)
    {
        require_once("util/ExportUsersReport.inc.php");
        $phpExcel = new exportUsersReport();
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        list($result, $countResult) = $usersListTableDAO->searchUsers($name, $lastName, $country, $userRoles, 1, false, $contextId);
        $phpExcel->exportAllUsers($result);
    }
    //genera la consulta del filtro según los parametros entrantes
    public function generateSearchFilter($name, $lastName, $country, $userRoles, $currentPage,$contextId)
    {   
        if (($name or $lastName  or $country or $userRoles) != null) {
            $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
           
            list($result, $countResult) = $usersListTableDAO->searchUsers($name, $lastName, $country, $userRoles, $currentPage, true, $contextId);
            return array($result, $countResult);
        }
        return null;
    }
    //llama la consulta de todos los usuarios
    public function getAllUsers($currentPage,$contextId)
    {
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $result = $usersListTableDAO->getAllUsers($currentPage,$contextId);

        return $result;
    }
    //método para controlar la paginación
    public function paginationControl($currentPage, $totalPages)
    {   
        $urlActual = $_SERVER['REQUEST_URI'];
        if (strpos($urlActual, "page=")) {
            $posicion = strpos($urlActual, "page=") + strlen("page="); // Busca la posición de "page="
            $urlActual = substr($urlActual, 0, $posicion);
        } else if (strpos($urlActual, "?")) {
            $urlActual = $urlActual . "&page=";
        } else {
            $urlActual = $urlActual . "?page=";
        }
        $paginationControl = '<ul class="pagination">';

        if ($currentPage > 1) {
            $paginationControl .= '<li><a href="' . $urlActual . '' . (1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;&laquo;</span></a></li>';
            $paginationControl .= '<li><a href="' . $urlActual . '' . ($currentPage - 1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        }

        for ($i = ($currentPage - 5); $i <= $totalPages; $i++) {
            if ($i > 0 && ($i < ($currentPage + 10))) {
                if ($currentPage == $i) {
                    $paginationControl .= '<li class="active"><a href="">' . ($i) . '<span class="sr-only">(current)</span></a></li>';
                } else {
                    $paginationControl .= '<li><a href="' . $urlActual . '' . ($i) . '">' . ($i) . "</a></li>";
                }
            }
        }

        if ($currentPage < $totalPages) {
            $paginationControl .= '<li><a href="' . $urlActual . ($currentPage + 1) . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
            $paginationControl .= '<li><a href="' . $urlActual . '' . $totalPages . '" aria-label="Next"><span aria-hidden="true">&raquo;&raquo;</span></a>';
        }

        $paginationControl .= "</ul>";

        return $paginationControl;
    }
    
    public function getTotalPages($contextId)
    {
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $totalPages = ceil(($usersListTableDAO->countUsers($contextId)) / 10); /*consulta el total de usuarios
                                                                     divide el numero entre 10 y lo aproxima
                                                                     con el método ceil                               */
        return $totalPages;
    }



    public function updateUniversity($user_id, $university)
    {
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $rowsAffected = $usersListTableDAO->updateUniversity($user_id, $university);
    }
    public function updateBiography($user_id, $biography)
    {
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $rowsAffected = $usersListTableDAO->updateBiography($user_id, $biography);
    }
    public function updateAcademicDegree($user_id, $newAcademicDegree)
    {
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        if ($usersListTableDAO->userHasAcademicDegree($user_id)) {
            $rowsAffected = $usersListTableDAO->updateAcademicDegree($user_id, $newAcademicDegree);
        } else {
            $rowsAffected = $usersListTableDAO->insertAcademicDegree($user_id, $newAcademicDegree);
        }
    }
    public function queryReviewAssignments($user_id)
    {
        $reviewerAssignmentsDAO = DAORegistry::getDAO("ReviewerAssignmentsDAO");
        $completedReviews = $reviewerAssignmentsDAO->countCompletedReviews($user_id);
        $activeReviews = $reviewerAssignmentsDAO->countActiveReviews($user_id);
        $rejectedReviews = $reviewerAssignmentsDAO->countRejectedReviews($user_id);
        $DaysSinceLastReview = $reviewerAssignmentsDAO->countDaysSinceLastReview($user_id);
        $DaysToCompleteReviews = $reviewerAssignmentsDAO->avgDaysToCompleteReviews($user_id);
        return array($completedReviews, $activeReviews, $rejectedReviews, $DaysSinceLastReview, $DaysToCompleteReviews);
    }
    public function queryAuthorActivity($email)
    {
        $authorActivityDAO = DAORegistry::getDAO("AuthorActivityDAO");
        $publicationsSended = $authorActivityDAO->publicationSended($email);
        $queuedPublications = $authorActivityDAO->publicationQueued($email);
        $publicationsAcepted = $authorActivityDAO->publicationAccepted($email);
        $publicationsRejected = $authorActivityDAO->publicationRejected($email);
        $scheduledPublications = $authorActivityDAO->publicationScheduled($email);
        return array($publicationsSended, $queuedPublications, $publicationsAcepted, $publicationsRejected, $scheduledPublications);
    }
    public function exportUsers($selectedUsers,$contextId)
    {
        require_once("util/ExportUsersReport.inc.php");
        $phpExcel = new exportUsersReport();
        $phpExcel->exportUsers($selectedUsers,$contextId);
    }
    public function generateDataFromChart($role,$templateMgr,$contextId){
        $usersListTableDAO=DAORegistry::getDAO("UsersListTableDAO");
        list($labels,$data)=$usersListTableDAO->getRolesByCountry($role,$contextId);
        $jsonLabels=json_encode($labels);
        $jsonData=json_encode($data);
        
        $templateMgr->assign("labels", $jsonLabels);
        $templateMgr->assign("data", $jsonData);
    }
}
