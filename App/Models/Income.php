<?php

namespace App\Models;

use \App\Mail;
use \Core\View;
use \App\Date;

use PDO;

/**
 * Income model
 */

 class Income extends \Core\Model {

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
    public $incomeCategoryAssignedToUserId;

    /**
     * Income amount
     * @var double
     */
    public $amount;

    /**
     * Income date
     * @var string
     */
    public $dateOfIncome;

    /**
     * Income comment
     * @var string
     */
    public $incomeComment;

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
     * Save the income model with the current property values
     * 
     * @return boolean True if the income was saved, false otherwise
     */
    public function save() {

    $this -> validate();

       if (empty($this -> errors)) {

            $sql = 'INSERT INTO incomes (userId, incomeCategoryAssignedToUserId, amount, dateOfIncome, incomeComment)
                    VALUES (:userId, :incomeCategoryAssignedToUserId, :amount, :dateOfIncome, :incomeComment)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':userId', $this -> userId, PDO::PARAM_INT);
            $stmt -> bindValue(':incomeCategoryAssignedToUserId', $this -> incomeCategoryAssignedToUserId, PDO::PARAM_INT);
            $stmt -> bindValue(':amount', $this -> amount, PDO::PARAM_STR);
            $stmt -> bindValue(':dateOfIncome', $this -> dateOfIncome, PDO::PARAM_STR);
            $stmt -> bindValue(':incomeComment', $this -> incomeComment, PDO::PARAM_STR);

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
        if ($this -> dateOfIncome != "") {
            $dateAndTime = date_create_from_format('Y-m-d', $this -> dateOfIncome);
            
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

        if (strtotime($this -> dateOfIncome) < strtotime('2000-01-01')) {
            $this -> errors[] = "Date must be equal or greather than 01-01-2000";
        }

        if (strtotime($this -> dateOfIncome) > strtotime(Date::getCurrentDate())) {
            $this -> errors[] = "Date must be equal or earlier than current date";
        }

        // Source
        if ((preg_match('/^[1-9][0-9]*/', $this -> incomeCategoryAssignedToUserId)) == 0) {
            $this -> errors[] = 'Income category invalid';
        } 

      }

 }