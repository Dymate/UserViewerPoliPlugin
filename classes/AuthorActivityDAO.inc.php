<?php
/*
 *
 * Módolo de Gestión para la búsqueda de usuarios
 * Dylan Mateo Llano Jaramillo & Juan José Restrepo Correa
 * Politécnico Colombiano Jaime Isaza Cadavid
 * Medellín-Colombia Mayo de 2023
 *
 */

import('lib.pkp.classes.db.DAO');

class AuthorActivityDAO extends DAO{
   
//consulta las publicaciones enviadas por el usuario
public function publicationSended($email){

    $result = $this->retrieveRange(
            'SELECT count(*) as publication_sended
            FROM users u
            JOIN authors a ON u.email = a.email
            JOIN publications p ON a.publication_id = p.publication_id
            where u.email=? ', array($email)
    );
    #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1
    # y se le extrae el valor para convertirlo en entero
    $publicationSended = intval(iterator_to_array($result)[0]->publication_sended);
    return $publicationSended;
}
//consulta las publicaciones en cola por el usuario
public function publicationQueued($email){

    $result = $this->retrieveRange(
            'SELECT count(*) as publication_queued
            FROM users u
            JOIN authors a ON u.email = a.email
            JOIN publications p ON a.publication_id = p.publication_id
            where u.email=? and status=1 ', array($email)
    );
    #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1
    # y se le extrae el valor para convertirlo en entero
    $publicationQueued = intval(iterator_to_array($result)[0]->publication_queued);
    return $publicationQueued;
}
//consulta las publicaciones aceptadas al usuario
public function publicationAccepted($email){

    $result = $this->retrieveRange(
            'SELECT count(*) as publicationAccepted
            FROM users u
            JOIN authors a ON u.email = a.email
            JOIN publications p ON a.publication_id = p.publication_id
            where u.email=? and status=3 ', array($email)
    );
    #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1
    # y se le extrae el valor para convertirlo en entero
    $publicationAccepted = intval(iterator_to_array($result)[0]->publicationAccepted);
    return $publicationAccepted;
}
//consulta las publicaciones rechazadas al usuario
public function publicationRejected($email){

    $result = $this->retrieveRange(
            'SELECT count(*) as publicationRejected
            FROM users u
            JOIN authors a ON u.email = a.email
            JOIN publications p ON a.publication_id = p.publication_id
            where u.email=? and status=4 ', array($email)
    );
    #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1
    # y se le extrae el valor para convertirlo en entero
    $publicationRejected = intval(iterator_to_array($result)[0]->publicationRejected);
    return $publicationRejected;
}
//consulta las publicaciones agendadas al usuario
public function publicationScheduled($email){

    $result = $this->retrieveRange(
            'SELECT count(*) as publicationScheduled
            FROM users u
            JOIN authors a ON u.email = a.email
            JOIN publications p ON a.publication_id = p.publication_id
            where u.email=? and status=5 ', array($email)
    );
    #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1
    # y se le extrae el valor para convertirlo en entero
    $publicationScheduled = intval(iterator_to_array($result)[0]->publicationScheduled);
    return $publicationScheduled;
}

}