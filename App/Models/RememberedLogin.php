<?php

namespace App\Models;   

use \App\Token;

use PDO;

/**
 * Remembered login model
 */

 class RememberedLogin extends \Core\Model {

    /**
     * Token hash
     * @var string
     */
    public $tokenHash;

    /**
     * User id
     * @var integer
     */
    public $userId;

    /**
     * Expiry date of token
     * @var string
     */
    public $expiresAt;

    /**
     * Find a remembered login model by the token
     * 
     * @param string $token The remembered token login
     * 
     * @return mixed Remembered login object if found, false otherwise
     * 
     */
    public static function findByToken($token) {

        $token = new Token($token);
        $tokenHash = $token -> getHashedTokenValue();

        $sql = 'SELECT * FROM remembered_logins WHERE tokenHash = :tokenHash';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':tokenHash', $tokenHash, PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetch();
    }

    /**
     * Get the user model associated with remembered login
     * 
     * @return User The user model
     * 
     */
    public function getUser() {

        return User::findById($this -> userId);

    }

    /**
     * Check if remembered token has expired
     * 
     * @return Boolean True if the token has expired, false otherwise
     * 
     */
    public function hasExpired() {

        return strtotime($this -> expiresAt) < time();

    }

    /**
     * Delete this model
     * 
     * @return void
     * 
     */
    public function delete() {

        $sql = 'DELETE FROM remembered_logins WHERE tokenHash = :tokenHash';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':tokenHash', $this -> tokenHash, PDO::PARAM_STR);
   
        $stmt -> execute();
        
    }

 }