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
     * @return class UsersListTable extends DataObject {

     */
    public function _fromRow($row)
    {   
        $usersListTable = $this->newDataObject();
        $usersListTable ->setUserId($row->user_id);
        $usersListTable->setFirstName($row->firstName);
        $usersListTable->setLastName($row->lastName);
        $usersListTable->setUserName($row->username);
        $usersListTable->setEmail($row->email);
        $usersListTable->setCountry($row->country);
        $usersListTable->setRoles($row->roles);
        
        
        return $usersListTable;
    }

    public function getAllUsers($page = 1)
    {
        $result = $this->retrieveRange(
            'SELECT u.user_id, 
                MAX(CASE WHEN us.setting_name = "givenName" THEN us.setting_value END) AS firstName,
                MAX(CASE WHEN us.setting_name = "familyName" THEN us.setting_value END) AS lastName,
                u.username,
                u.email,
                u.country,
                GROUP_CONCAT(DISTINCT r.role_id SEPARATOR ",") AS roles
                FROM users u 
                LEFT JOIN user_settings us ON u.user_id = us.user_id
                LEFT JOIN roles r ON u.user_id = r.user_id
                GROUP BY u.user_id
                LIMIT 0, 10;'
            ,array((($page - 1) * 10))
        );
        
        
        $returner = [];
        foreach ($result as $data) {
           $returner[] = $this->_fromRow($data);
        }
        #$result->Close();
        return $returner;
    }

    
}