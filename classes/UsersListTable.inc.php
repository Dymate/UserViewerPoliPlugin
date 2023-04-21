<?php

class UsersListTable extends DataObject
{ 
    /* Getter Setter Id usuario*/
    public function getUserId(){
        return $this->getData('user_id');
    }
    public function setUserId($userId)
    {
        return $this->setData('user_id', $userId);
    }

    
    /* Getter Setter Nombre*/
    public function getFirstName(){
        return $this->getData('firstName');
    }
    public function setFirstName($firstName)
    {
        return $this->setData('firstName', $firstName);
    }
    
    /* Getter Setter Apellido*/
    public function getLastName(){
        return $this->getData('lastName');
    }
    public function setLastName($lastName)
    {
        return $this->setData('lastName', $lastName);
    }

    /* Getter Setter Nombre de usuario*/
    public function getUserName(){
        return $this->getData('username');
    }
    public function setUserName($userName)
    {
        return $this->setData('username', $userName);
    }
    /* Getter Setter universidad*/
    public function getUniversity(){
        return $this->getData('university');
    }
    public function setUniversity($university)
    {
        return $this->setData('university', $university);
    }
    //getter Setter grado academico
    public function getAcademicDegree(){
        return $this->getData('academicDegree');
    }
    public function setAcademicDegree($academicDegree)
    {
        return $this->setData('academicDegree', $academicDegree);
    }
    //getter Setter biografía
    public function getBiography(){
        return $this->getData('biography');
    }
    public function setBiography($biography)
    {
        return $this->setData('biography', $biography);
    }
    /* Getter Setter Correo*/
     public function getEmail(){
        return $this->getData('email');
    }
    public function setEmail($email)
    {
        return $this->setData('email', $email);
    }
    /* Getter Setter País*/

    public function getCountry(){
        return $this->getData('country');
    }
    public function setCountry($country)
    {
        return $this->setData('country', $country);
    }
    public function getRoles(){
        return $this->getData('roles');
    }
    
    public function setRoles($roles){
        return $this->setData('roles',$roles);
    }
}