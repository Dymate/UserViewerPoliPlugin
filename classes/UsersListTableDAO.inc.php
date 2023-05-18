<?php

import('lib.pkp.classes.db.DAO');
import('plugins.generic.userViewerPoliPlugin.classes.UsersListTable');
class UsersListTableDAO extends DAO
{


    /**
     * Generate a new object.
     * @return UsersListTable
     */
    public function newDataObject()
    {
        return new UsersListTable();
    }


    /**
     * Return a new object from a given row.
     * @return UsersListTable extends DataObject {

     */
    public function _fromRow($row)
    {
        $usersListTable = $this->newDataObject();
        $usersListTable->setUserId($row->user_id);
        $usersListTable->setFirstName($row->firstName);
        $usersListTable->setLastName($row->lastName);
        $usersListTable->setUniversity($row->university);
        $usersListTable->setAcademicDegree($row->academicDegree);
        $usersListTable->setBiography($row->biography);
        $usersListTable->setUserName($row->username);
        $usersListTable->setEmail($row->email);
        $usersListTable->setCountry($row->country);
        $usersListTable->setRoles($row->roles);


        return $usersListTable;
    }

    public function getAllUsers($page)
    {
        $result = $this->retrieveRange(
            "SELECT u.user_id, 
                MAX(CASE WHEN us.setting_name = 'givenName' THEN us.setting_value END) AS firstName,
                MAX(CASE WHEN us.setting_name = 'familyName' THEN us.setting_value END) AS lastName,
                MAX(CASE WHEN us.setting_name= 'affiliation' THEN us.setting_value END) as university,
                MAX(CASE WHEN us.setting_name= 'academicDegree' THEN us.setting_value END) as academicDegree,
                MAX(CASE WHEN us.setting_name= 'biography' THEN us.setting_value END) as biography,
                u.username,
                u.email,
                u.country,
                GROUP_CONCAT(DISTINCT uug.user_group_id SEPARATOR ',') AS roles
                FROM users u 
                LEFT JOIN user_settings us ON u.user_id = us.user_id
                LEFT JOIN user_user_groups uug ON u.user_id = uug.user_id
                GROUP BY u.user_id
                limit ?,10;",
            array((($page - 1) * 10))
        );

        $returner = [];
        foreach ($result as $data) {
            $returner[] = $this->_fromRow($data);
        }
        #$result->Close();
        return $returner;
    }

    public function searchUsers($name, $lastName, $country, $userRoles, $page, $isToFilter)
    {
        $sql = "SELECT	search.user_id,search.firstName, search.lastName, search.university, search.academicDegree,search.biography, search.username, search.email,search.country, search.roles
        FROM (  
            SELECT u.user_id,
                   MAX(CASE WHEN us.setting_name = 'givenName' THEN us.setting_value END) AS firstName,
                   MAX(CASE WHEN us.setting_name = 'familyName' THEN us.setting_value END) AS lastName,
                   MAX(CASE WHEN us.setting_name= 'affiliation' THEN us.setting_value END) as university,
                   MAX(CASE WHEN us.setting_name= 'academicDegree' THEN us.setting_value END) as academicDegree,
                   MAX(CASE WHEN us.setting_name= 'biography' THEN us.setting_value END) as biography,
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
        if ($country) {
            $sql .= "AND search.country LIKE '%$country%'";
        }
        if ($userRoles) {
            if ($userRoles == 1) {
                $sql .= "AND search.roles LIKE '%1,%'";
            } else {
                $sql .= "AND search.roles =".$userRoles." ";
            }
        }
        $countResult = $this->countFilteredUsers($sql);
        if ($isToFilter) {
            $sql .= "limit ?,10";
        }

        $result = $this->retrieveRange($sql, array((($page - 1) * 10)));
        $returner = [];
        foreach ($result as $data) {

            $returner[] = $this->_fromRow($data);
        }
        #$result->Close();
        return array($returner, $countResult);
    }
    public function countUsers()
    {
        $result = $this->retrieveRange(
            'SELECT count( u.user_id) as users
         FROM users u '
        );
        #Se convierte lazyCollection en array, se obtiene el stdObject de la posición 1 y se le extrae el valor y se convierte en entero

        $totalUsers = intval(iterator_to_array($result)[0]->users);
        return $totalUsers;
    }
    public function updateUniversity($userId, $newUniversity)
    {
        $rowsAffected = $this->update(
            'Update user_settings 
            Set setting_value=?
            where user_id=? AND setting_name="affiliation"',
            array($newUniversity, $userId)
        );
        return $rowsAffected;
    }
    public function updateAcademicDegree($userId, $newAcademicDegree)
    {
        $rowsAffected = $this->update(
            'Update user_settings 
            Set setting_value=?
            where user_id=? AND setting_name="academicDegree"',
            array($newAcademicDegree, $userId)
        );
        return $rowsAffected;
    }
    public function updateBiography($userId, $biography)
    {
        $rowsAffected = $this->update(
            'Update user_settings 
            Set setting_value=?
            where user_id=? AND setting_name="biography"',
            array($biography, $userId)
        );
        return $rowsAffected;
    }
    public function insertAcademicDegree($userId, $newAcademicDegree)
    {
        $rowsAffected = $this->update(
            "INSERT INTO user_settings (user_id,setting_name, setting_value,setting_type) VALUES (?, 'academicDegree', ?,'string');",
            array($userId, $newAcademicDegree)
        );
        return $rowsAffected;
    }
    public function userHasAcademicDegree($user_id)
    {
        $result = $this->retrieveRange(
            'Select count(*) as results
            from user_settings 
            where user_id=? and setting_name="academicDegree" ',
            array($user_id)
        );
        #Se convierte lazyCollection en array, se obtiene el stdObject de la posición 1 y se le extrae el valor y se convierte en entero

        $resultCounted = intval(iterator_to_array($result)[0]->results);
        if ($resultCounted > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function countFilteredUsers($sql) //se usa para contar el total de usuarios generados por el filtro
    {
        $changedSql = str_replace(
            "search.user_id,search.firstName, search.lastName, search.university, search.academicDegree,search.biography, search.username, search.email,search.country, search.roles",
            " count(*) as results",
            $sql
        );
        $result = $this->retrieveRange($changedSql);
        $resultCounted = intval(iterator_to_array($result)[0]->results);
        return $resultCounted;
    }
    public function getUserByID($userId)
    {
        $result = $this->retrieveRange(
            'SELECT u.user_id, 
                MAX(CASE WHEN us.setting_name = "givenName" THEN us.setting_value END) AS firstName,
                MAX(CASE WHEN us.setting_name = "familyName" THEN us.setting_value END) AS lastName,
                MAX(CASE WHEN us.setting_name= "affiliation" THEN us.setting_value END) as university,
                MAX(CASE WHEN us.setting_name= "academicDegree" THEN us.setting_value END) as academicDegree,
                MAX(CASE WHEN us.setting_name= "biography" THEN us.setting_value END) as biography,
                u.username,
                u.email,
                u.country,
                GROUP_CONCAT(DISTINCT uug.user_group_id SEPARATOR ",") AS roles
                FROM users u 
                LEFT JOIN user_settings us ON u.user_id = us.user_id
                LEFT JOIN user_user_groups uug ON u.user_id = uug.user_id
                WHERE u.user_id=?
                GROUP BY u.user_id
                ',
            array($userId)
        );

        $returner = [];
        foreach ($result as $data) {
            $returner = $this->_fromRow($data);
        }
        #$result->Close();
        return $returner;
    }

    public function getRolesByCountry($role)
    {
        $sql = "SELECT search.country, COUNT(search.country) as cant
        FROM (
            SELECT u.user_id, 
                MAX(CASE WHEN us.setting_name = 'givenName' THEN us.setting_value END) AS firstName,
                MAX(CASE WHEN us.setting_name = 'familyName' THEN us.setting_value END) AS lastName,
                MAX(CASE WHEN us.setting_name = 'affiliation' THEN us.setting_value END) AS university,
                MAX(CASE WHEN us.setting_name = 'academicDegree' THEN us.setting_value END) AS academicDegree,
                MAX(CASE WHEN us.setting_name = 'biography' THEN us.setting_value END) AS biography,
                u.username,
                u.email,
                u.country,
                GROUP_CONCAT(DISTINCT uug.user_group_id SEPARATOR ',') AS roles
            FROM users u 
            LEFT JOIN user_settings us ON u.user_id = us.user_id
            LEFT JOIN user_user_groups uug ON u.user_id = uug.user_id
            GROUP BY u.user_id, u.country, u.username, u.email
        ) AS search
        WHERE 1=1 ";
        if ($role != null) {
            if ($role == 1) {
                $sql .= "AND search.roles LIKE '%1,%'";
            } else {
                $sql .= "AND search.roles LIKE '%" . $role . "%'";
            }
        }
        $sql .= " GROUP BY search.country";

        $result = $this->retrieveRange(
            $sql,
            array($role)
        );
        $countries = [];
        $cant = [];

        foreach ($result as $data) {
            if ($data->country == "") {
                $sinDato = 'sin país';
                $countries[] = $sinDato;
                $cant[] = $data->cant;
            } else {
                $countries[] = $data->country;
                $cant[] = $data->cant;
            }
        }
        return array($countries, $cant);
    }
}
