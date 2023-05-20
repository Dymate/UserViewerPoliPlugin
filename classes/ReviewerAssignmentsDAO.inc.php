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


class ReviewerAssignmentsDAO extends DAO{
 
    //cuenta las evaluaciones completadas por el usuario
    public function countCompletedReviews($user_id){
        $result = $this->retrieveRange(
            'SELECT COUNT(CASE WHEN ra.date_completed IS NOT NULL AND ra.declined = 0 THEN 1 END) AS complete_count
            from review_assignments as ra
            where reviewer_id=? ', array($user_id)
        );
        #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1
        # y se le extrae el valor para convertirlo en entero
        $completedReviews = intval(iterator_to_array($result)[0]->complete_count);
        return $completedReviews;
    }
 //cuenta las evaluaciones activas por el usuario
    public function countActiveReviews($user_id){
        $result = $this->retrieveRange(
            'SELECT COUNT(CASE WHEN ra.date_completed IS NULL AND ra.declined = 0 THEN 1 END) AS active_reviews
            from review_assignments as ra
            where reviewer_id=? ', array($user_id)
        );
        #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1 
        #y se le extrae el valor para convertirlo en entero
        $activeReviews = intval(iterator_to_array($result)[0]->active_reviews);
        return $activeReviews;
    }
     //cuenta las evaluaciones rechazadas por el usuario
    public function countRejectedReviews($user_id){
        $result = $this->retrieveRange(
            'SELECT COUNT(CASE WHEN  ra.declined = 1 THEN 1 END) AS rejected_reviews
            from review_assignments as ra
            where reviewer_id=? ', array($user_id)
        );
        #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1 
        #y se le extrae el valor para convertirlo en entero
        $rejectedReviews = intval(iterator_to_array($result)[0]->rejected_reviews);
        return $rejectedReviews;
    }
     //cuenta los días desde la ultima revision
    public function countDaysSinceLastReview($user_id){
        $result = $this->retrieveRange(
            'SELECT DATEDIFF(NOW(),MAX(CASE WHEN ra.reviewer_id=? then ra.date_assigned END))as last_assignment
            from review_assignments as ra', array($user_id)
        );
        #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1 
        #y se le extrae el valor para convertirlo en entero
        $daysSinceLastReview = intval(iterator_to_array($result)[0]->last_assignment);
        return $daysSinceLastReview;
    }
    //cuenta los dias promedios para cada revisión
    public function avgDaysToCompleteReviews($user_id){
        $result = $this->retrieveRange(
            'SELECT AVG(DATEDIFF(ra.date_completed, ra.date_notified)) AS average_time
            from review_assignments as ra
            where reviewer_id=?', array($user_id)
        );
        #Se convierte lazyCollection en array, se obtiene el objeto de la posición 1 
        #y se le extrae el valor para convertirlo en entero
        $averageTime = intval(iterator_to_array($result)[0]->average_time);
        return $averageTime;
    }
}
