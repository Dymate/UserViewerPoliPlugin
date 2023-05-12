<?php

use PhpOption\None;

use function PHPSTORM_META\type;

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
        if (count(array_intersect(array(ROLE_ID_SITE_ADMIN), $roles)) == 0) {
            header('HTTP/1.0 403 Forbidden');
            return print('<h2>Acceso denegado</h2>No tienes permitido ingresar a esta sección.');
        }
        //DECLARACION DE VARIABLES
        $userListComplements = $this->newUserListComplement();
        $generateUsersTable = $this->newGenerateUsersTable();
        $plugin = PluginRegistry::getPlugin("generic", "userviewerpoliplugin");
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->addJavaScript('usersListTable', $plugin->getPluginBaseUrl() . '/js/usersListTable.js');
        $templateMgr->assign("userRoles", $roles); //Necesario para el botón usuarios
        $currentPage = isset($_GET["page"]) ? $_GET["page"] : 1;
        $data = $this->getAllUsers($currentPage);
        $templateMgr->assign("usersTable", $generateUsersTable->listUsers($data));
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
        if (isset($_SESSION['selectedValues'])) {
            $needExport = isset($_POST['selectedValues']) ? $_SESSION['selectedValues'] . $_POST['selectedValues'] : null;
            $_SESSION['selectedValues'] = $needExport;
        } else {
            $needExport = isset($_POST['selectedValues']) ? $_POST['selectedValues'] : null;
            $_SESSION['selectedValues'] = $needExport;
        }



        //ASIGNACION DE VARIABLES DE LA TEMPLATE
        $templateMgr->assign("selectedCountryValue", $country);
        $templateMgr->assign("selectedRolesValue", $userRoles);
        $templateMgr->assign("optionsCountry", $optionsCountry);
        $templateMgr->assign("optionsRoles", $optionsRoles);
        //$selectedUsers=isset($_POST['selectedValues']) ? $_POST['selectedValues'] : null;

        $needExport != null ? $this->exportUsers($needExport) : null;
        list($data, $countResult) = $this->generateSearchFilter($name, $lastName, $country, $userRoles, $currentPage);
        if (isset($data)) {
            $templateMgr->assign("usersTable", $generateUsersTable->listUsers($data));
            $totalPages = ceil(($countResult) / 10);
        } else {
            $totalPages = $this->getTotalPages();
        }



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

        $templateMgr->assign("paginationControl", $this->paginationControl($currentPage, $totalPages));

        return $templateMgr->display($plugin->getTemplateResource("usersListTable.tpl"));
    }

    public function generateSearchFilter($name, $lastName, $country, $userRoles, $currentPage)
    {
        if (($name or $lastName  or $country or $userRoles) != null) {
            $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
            list($result, $countResult) = $usersListTableDAO->searchUsers($name, $lastName, $country, $userRoles, $currentPage);
            return array($result, $countResult);
        }
        return null;
    }
    public function getAllUsers($currentPage)
    {
        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $result = $usersListTableDAO->getAllUsers($currentPage);

        return $result;
    }

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

    public function getTotalPages()
    {
        //$search = isset($_POST["search"]) ? $_POST["search"] : '';

        $usersListTableDAO = DAORegistry::getDAO("UsersListTableDAO");
        $totalPages = ceil(($usersListTableDAO->countUsers()) / 10);
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
    public function exportUsers($selectedUsers)
    {
        require_once("util/exportUsersReport.inc.php");
        $phpExcel = new exportUsersReport();
        $phpExcel->exportUsers($selectedUsers);
    }
}
