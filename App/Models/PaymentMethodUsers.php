<?php

namespace App\Models;

use PDO;

/**
 * Income model
 */

 class PaymentMethodUsers extends \Core\Model {

     /**
     * Payment method id
     * @var int
     */
    public $id;

    /**
     * User id
     * @var int
     */
    public $userId;

    /**
     * Payment methods assigned to user id 
     * @var int
     */
    public $name;

    /**
     * Get all methods assigned to user
     * 
     * @return array
     * 
     */
    static public function getAll($loggedUserId) {

        $sql = 'SELECT id, name FROM payment_methods_assigned_to_users WHERE userId = :loggedUserId';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $loggedUserId, PDO::PARAM_INT);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetchAll();

    }

 }