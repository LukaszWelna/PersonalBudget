<?php

namespace App\Models;

use \App\Mail;
use \Core\View;
use \App\Date;

use PDO;

/**
 * Income model
 */

 class IncomeCategoryUsers extends \Core\Model {

     /**
     * Income id
     * @var int
     */
    public $id;

    /**
     * UserId
     * @var int
     */
    public $userId;

    /**
     * Income category assigned to user id 
     * @var int
     */
    public $name;

    /**
     * Get all categories assigned to user
     * 
     * @return void
     * 
     */
    static public function getAll($loggedUserId) {

        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE userId = :loggedUserId';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $loggedUserId, PDO::PARAM_INT);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetchAll();

    }

 }