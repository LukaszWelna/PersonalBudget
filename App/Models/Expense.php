<?php

namespace App\Models;

use \App\Mail;
use \Core\View;
use \App\Date;

use PDO;

/**
 * Income model
 */

 class Expense extends \Core\Model {

     /**
     * Expense id
     * @var int
     */
    public $id;

    /**
     * UserId
     * @var int
     */
    public $userId;

    /**
     * Expense category assigned to user id 
     * @var int
     */
    public $expenseCategoryAssignedToUserId;

    /**
     * Payment method assigned to user id 
     * @var int
     */
    public $paymentMethodAssignedToUserId;

    /**
     * Expense amount
     * @var double
     */
    public $amount;

    /**
     * Expense date
     * @var string
     */
    public $dateOfExpense;

    /**
     * Expense comment
     * @var string
     */
    public $expenseComment;

    /**
     * Error messages
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     * 
     */
    public function __construct($userId, $data = []) {

        foreach ($data as $key => $value) {
            $this -> $key = $value;
        }

        $this -> userId = $userId;

    }

     /**
     * Save the expense model with the current property values
     * 
     * @return boolean True if the expense was saved, false otherwise
     */
    public function save() {

    $this -> validate();

       if (empty($this -> errors)) {

            $sql = 'INSERT INTO expenses (userId, expenseCategoryAssignedToUserId, paymentMethodAssignedToUserId, 
                                            amount, dateOfExpense, expenseComment)
                    VALUES (:userId, :expenseCategoryAssignedToUserId, :paymentMethodAssignedToUserId, 
                            :amount, :dateOfExpense, :expenseComment)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':userId', $this -> userId, PDO::PARAM_INT);
            $stmt -> bindValue(':expenseCategoryAssignedToUserId', $this -> expenseCategoryAssignedToUserId, PDO::PARAM_INT);
            $stmt -> bindValue(':paymentMethodAssignedToUserId', $this -> paymentMethodAssignedToUserId, PDO::PARAM_INT);
            $stmt -> bindValue(':amount', $this -> amount, PDO::PARAM_STR);
            $stmt -> bindValue(':dateOfExpense', $this -> dateOfExpense, PDO::PARAM_STR);
            $stmt -> bindValue(':expenseComment', $this -> expenseComment, PDO::PARAM_STR);

            return $stmt -> execute();

        }

        return false;
        
     }

      /**
      * Validate current property values, adding validation error messages to the errors array property 
      *
      * @return void
      */
      public function validate() {
       
        // Amount
        if ($this -> amount == '') {
            $this -> errors[] = 'Amount is required';
        }

        if ($this -> amount <= 0 && $this -> amount != '') {
            $this -> errors[] = 'Amount must be greather than 0';
        }

        if ((preg_match('/[0-9]*\.[0-9]{3,}/', $this -> amount)) == 1) {
            $this -> errors[] = 'Amount must contain max 2 digits after decimal point';
        }

        if ((preg_match('/^[0-9]{9,}/', $this -> amount)) == 1) {
            $this -> errors[] = 'Amount must contain max 8 digits before decimal point';
        }

        // Date
        if ($this -> dateOfExpense != "") {
            $dateAndTime = date_create_from_format('Y-m-d', $this -> dateOfExpense);
            
            if ($dateAndTime === false) {
                $this -> errors[] = "Invalid date";
            }
            else {
                $dateErrors = date_get_last_errors();

                if ($dateErrors != NULL && $dateErrors["warning_count"]) {
                    $this -> errors[] = "Invalid date";
                }
            }
        } else {
            $this -> errors[] = "Date is required";
        }

        if (strtotime($this -> dateOfExpense) < strtotime('2000-01-01')) {
            $this -> errors[] = "Date must be equal or greather than 01-01-2000";
        }

        if (strtotime($this -> dateOfExpense) > strtotime(Date::getCurrentDate())) {
            $this -> errors[] = "Date must be equal or earlier than current date";
        }

        // Payment
        if ((preg_match('/^[1-9][0-9]*/', $this -> paymentMethodAssignedToUserId)) == 0) {
            $this -> errors[] = 'Payment method invalid';
        } 

        // Source
        if ((preg_match('/^[1-9][0-9]*/', $this -> expenseCategoryAssignedToUserId)) == 0) {
            $this -> errors[] = 'Expense category invalid';
        } 

      }

 }