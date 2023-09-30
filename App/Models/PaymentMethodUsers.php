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
     * New payment method
     * @var string
     */
    public $newPaymentMethod;

    /**
     * Edit payment method
     * @var string
     */
    public $editPaymentMethod;

    /**
     * Payment method assigned to user id 
     * @var int
     */
    public $paymentMethodAssignedToUserId;

    /**
     * Adding payment method error messages 
     * @var array
     */
    public $addingErrors = [];

    /**
     * Editing payment method error messages 
     * @var array
     */
    public $editingErrors = [];

    /**
     * Deleting payment method messages 
     * @var array
     */
    public $deletingErrors = [];

    /**
     * Class constructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     * 
     */
    public function __construct($userId = [], $data = []) {

        foreach ($data as $key => $value) {
            $this -> $key = $value;
        }

        $this -> userId = $userId;

    }

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

     /**
     * 
     * Check if payment method name already exists in database
     * 
     * @param string $newPaymentMethod Payment method name to search for
     * 
     * @return boolean True if a record already exists with specified payment method name, false otherwise
     * 
     */

     public static function checkPaymentMethodNameExists($paymentMethods, $newPaymentMethod, $paymentMethodAssignedToUserId) {

        //Check if new expense category is unique
        foreach ($paymentMethods as $method) {
            if ((strtoupper($newPaymentMethod) == strtoupper($method -> name)) && ($paymentMethodAssignedToUserId != $method -> id)) {
                return true;
            }
        }
        return false;
     }

    /**
     * Save new payment method
     * 
     * @return boolean True if the payment method was saved, false otherwise
     */
    public function saveNewPaymentMethod($paymentMethodsAssignedToUser) {

        $this -> addingErrors = static::validateNewPaymentMethod($paymentMethodsAssignedToUser, $this -> newPaymentMethod);
    
           if (empty($this -> addingErrors)) {
    
                $sql = 'INSERT INTO payment_methods_assigned_to_users (userId, name)
                        VALUES (:userId, :name)';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':userId', $this -> userId, PDO::PARAM_INT);
                $stmt -> bindValue(':name', $this -> newPaymentMethod, PDO::PARAM_STR);
    
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
     * Edit payment method
     * 
     * @return boolean True if the payment method was edited, false otherwise
     */
    public function editPaymentMethod($paymentMethodsAssignedToUser) {

        $this -> editingErrors = static::validateNewPaymentMethod($paymentMethodsAssignedToUser, $this -> editPaymentMethod, 
                                                                    $this -> paymentMethodAssignedToUserId);
    
           if (empty($this -> editingErrors)) {
    
                $sql = 'UPDATE payment_methods_assigned_to_users
                        SET name = :name
                        WHERE id = :methodId';

                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':name', $this -> editPaymentMethod, PDO::PARAM_STR);
                $stmt -> bindValue(':methodId', $this -> paymentMethodAssignedToUserId, PDO::PARAM_INT);
        
                return $stmt -> execute();
            
            }
    
            return false;
            
         }

    /**
     * Delete payment method
     * 
     * @return boolean True if the payment method was deleted, false otherwise
     */
    public function deletePaymentMethod() {

        $this -> deletingErrors = static::validateNewPaymentMethod(0, 0, $this -> paymentMethodAssignedToUserId);
                                                            
           if (empty($this -> deletingErrors)) {
    
                $sql = 'DELETE FROM payment_methods_assigned_to_users
                        WHERE id = :categoryId';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':categoryId', $this -> paymentMethodAssignedToUserId, PDO::PARAM_INT);
                
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
      * Validate payment method
      *
      * @return array
      */
      static public function validateNewPaymentMethod($paymentMethodsAssignedToUser, $paymentMethod, 
                                                        $paymentMethodAssignedToUserId = 0) {

        $errors = [];

         //Check if chosen payment method is valid
         if ($paymentMethodAssignedToUserId != 0) {
            if ((preg_match('/^[1-9][0-9]*/', $paymentMethodAssignedToUserId)) == 0) {
                $errors[] = 'Payment method is invalid';
            } 
        }

        if ($paymentMethod != 0) {
            //Check if variable is not empty
            if ($paymentMethod == '') {
                $errors[] = 'New payment method is required.';
            }

            //Check numbers and special characters in payment method
            if ((preg_match('/^[A-Za-z\s]+$/', $paymentMethod)) == 0) {
                $errors[] = 'New payment method must contain only letters.';
            } 

            //Check number of characters (must be less than 25)
            if (strlen($paymentMethod) > 25) {
                $errors[] = 'New payment method can not contain more than 25 letters.';
            }
        }
       
        if (($paymentMethod != 0) && ($paymentMethodsAssignedToUser != 0)) {
            //Check if new payment method is unique
            if (static::checkPaymentMethodNameExists($paymentMethodsAssignedToUser, $paymentMethod, $paymentMethodAssignedToUserId) == true) {
                $errors[] = 'Payment method must has unique name.';
            }
        }

        return $errors;

      }


 }