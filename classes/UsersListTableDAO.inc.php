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
        $usersListTable->setCountry($row->country);
        $usersListTable->setRoles($row->roles);
        
        
        return $usersListTable;
    }

    public function getAllUsers($page)
    {
        $result = $this->retrieveRange(
                'SELECT u.user_id, 
                MAX(CASE WHEN us.setting_name = "givenName" THEN us.setting_value END) AS firstName,
                MAX(CASE WHEN us.setting_name = "familyName" THEN us.setting_value END) AS lastName,
                u.username,
                u.email,
                u.country,
                GROUP_CONCAT(DISTINCT uug.user_group_id SEPARATOR ",") AS roles
                FROM users u 
                LEFT JOIN user_settings us ON u.user_id = us.user_id
                LEFT JOIN user_user_groups uug ON u.user_id = uug.user_id
                GROUP BY u.user_id
                limit ?,10;'
            ,array((($page - 1) * 10))
        );
        
        $returner = [];
        foreach ($result as $data) {
           $returner[] = $this->_fromRow($data);
        }
        #$result->Close();
        return $returner;
    }
    
    public function searchUsers($sql){
        $result = $this->retrieveRange($sql);
        $returner = [];
        foreach ($result as $data) {
           
           $returner[] = $this->_fromRow($data);
        }
        #$result->Close();
        return $returner;
    }
    public function countUsers(){
        $result = $this->retrieveRange(
        'SELECT count( u.user_id) as users
         FROM users u '
        );
        #Se convierte lazyCollection en array, se obtiene el stdObject de la posición 1 y se le extrae el valor y se convierte en entero
        
        $totalUsers=intval(iterator_to_array($result)[0]->users);
        return $totalUsers;
    }
    
}