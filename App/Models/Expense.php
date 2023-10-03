<?php

namespace App\Models;

use \App\Mail;
use \Core\View;
use \App\Date;

use PDO;

/**
 * Expense model
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
     * @param integer $userId User id
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

            if (strtotime($this -> dateOfExpense) < strtotime('2000-01-01')) {
                $this -> errors[] = "Date must be equal or greather than 01-01-2000";
            }
    
            if (strtotime($this -> dateOfExpense) > strtotime(Date::getCurrentDate())) {
                $this -> errors[] = "Date must be equal or earlier than current date";
            }

        } else {
            $this -> errors[] = "Date is required";
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

        /**
       * Get all expenses in defined date period
       * 
       * @param integer $loggedUserId Logged user id
       * @param array $dates Dates range
       * 
       * @return mixed Array with all expenses, false if not found 
       */
      public static function getAll($loggedUserId, $dates) {

        $sql = 'SELECT expenses.*, expenses_category_assigned_to_users.name, payment_methods_assigned_to_users.name AS paymentName
                FROM expenses 
                INNER JOIN expenses_category_assigned_to_users 
                ON expenses.expenseCategoryAssignedToUserId = expenses_category_assigned_to_users.id
                INNER JOIN payment_methods_assigned_to_users 
                ON expenses.paymentMethodAssignedToUserId = payment_methods_assigned_to_users.id
                WHERE expenses.userId = :loggedUserId 
                AND expenses.dateOfExpense BETWEEN :startDate AND :endDate ORDER BY expenses.dateOfExpense DESC, expenses.amount DESC';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $loggedUserId, PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $dates['start'], PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $dates['end'], PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $stmt -> execute();

        return $stmt -> fetchAll();

      }

       /**
       * Get grouped expenses in defined date period
       * 
       * @param integer $loggedUserId Logged user id
       * @param array $dates Dates range
       * 
       * @return mixed Array with grouped expenses, false if not found  
       */
      public static function getGroupedUserExpenses($loggedUserId, $dates) {
 
        $sql = 'SELECT expenses_category_assigned_to_users.name AS label, SUM(expenses.amount) AS y
                FROM expenses_category_assigned_to_users
                INNER JOIN expenses ON expenses_category_assigned_to_users.id = expenses.expenseCategoryAssignedToUserId
                WHERE
                expenses.dateOfExpense BETWEEN :startDate AND :endDate
                AND expenses.userId = :loggedUserId
                GROUP BY label
                ORDER BY y DESC';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $loggedUserId, PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $dates['start'], PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $dates['end'], PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $stmt -> execute();

        return $stmt -> fetchAll();

      }

       /**
       * Get total amount of expenses
       * 
       * @param integer $loggedUserId Logged user id
       * @param array $dates Dates range
       * 
       * @return integer total amount of expenses
       */
      public static function getTotalAmount($loggedUserId, $dates) {
 
        $sql = 'SELECT SUM(expenses.amount) AS totalAmount
                FROM expenses 
                WHERE expenses.dateOfExpense BETWEEN :startDate AND :endDate
                AND expenses.userId = :loggedUserId';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $loggedUserId, PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $dates['start'], PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $dates['end'], PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $stmt -> execute();

        $data = $stmt -> fetch();

        if ($data['totalAmount']) {
            return $data['totalAmount'];
        } else {
            return 0;
        }

      }

       /**
       * 
       * Get total amount of expenses in given month of given category
       * 
       * @param integer $loggedUserId Logged user id
       * @param string $expenseCategoryAssignedToUserId Chosen category
       * @param array $date Chosen date
       * 
       * @return float total amount of expenses
       * 
       */
      public static function getTotalAmountOfCategoryInMonth($loggedUserId, $expenseCategoryAssignedToUserId, $date) {

        $dates = Date::getGivenMonthDates($date);
        $expenseCategoryAssignedToUserId = str_replace('-', ' ', $expenseCategoryAssignedToUserId);

        $sql = 'SELECT SUM(expenses.amount) AS totalAmount
                FROM expenses 
                INNER JOIN expenses_category_assigned_to_users ON expenses_category_assigned_to_users.id = expenses.expenseCategoryAssignedToUserId
                WHERE expenses.dateOfExpense BETWEEN :startDate AND :endDate
                AND expenses_category_assigned_to_users.name = :expenseCategoryAssignedToUserId
                AND expenses.userId = :loggedUserId';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':startDate', $dates['start'], PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $dates['end'], PDO::PARAM_STR);
        $stmt -> bindValue(':expenseCategoryAssignedToUserId', $expenseCategoryAssignedToUserId, PDO::PARAM_STR);
        $stmt -> bindValue(':loggedUserId', $loggedUserId, PDO::PARAM_INT);
        $stmt -> setFetchMode(PDO::FETCH_ASSOC);
        $stmt -> execute();

        $data = $stmt -> fetch();

        if ($data['totalAmount']) {
            return $data['totalAmount'];
        } else {
            return 0;
        }

  }

 }