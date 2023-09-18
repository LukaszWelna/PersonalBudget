<?php

namespace App\Models;

use PDO;

/**
 * Income model
 */

 class ExpenseCategoryUsers extends \Core\Model {

     /**
     * Income id
     * @var int
     */
    public $id;

    /**
     * User id
     * @var int
     */
    public $userId;

    /**
     * Income category assigned to user id 
     * @var int
     */
    public $name;

    /**
     * New expense category
     * @var string
     */
    public $newExpenseCategory;

    /**
     * New payment method
     * @var string
     */
    public $editExpenseCategory;

    /**
     * Expense category assigned to user id 
     * @var int
     */
    public $expenseCategoryAssignedToUserId;

    /**
     * Adding income error messages 
     * @var array
     */
    public $addingErrors = [];

    /**
     * Editing income error messages 
     * @var array
     */
    public $editingErrors = [];

    /**
     * Deleting expense error messages 
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
     * Get all categories assigned to user
     * 
     * @return array
     * 
     */
    static public function getAll($loggedUserId) {

        $sql = 'SELECT id, name FROM expenses_category_assigned_to_users WHERE userId = :loggedUserId';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $loggedUserId, PDO::PARAM_INT);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetchAll();

    }

     /**
     * 
     * Check if category name already exists in database
     * 
     * @param string $newCategoryName Category name to search for
     * 
     * @return boolean True if a record already exists with specified category name, false otherwise
     * 
     */

     public static function checkCategoryNameExists($expenseCategories, $newCategoryName) {

        //Check if new expense category is unique
        foreach ($expenseCategories as $category) {
            if (strtoupper($newCategoryName) == strtoupper($category -> name)) {
                return true;
            }
        }
        return false;
     }

    /**
     * Save new expense category
     * 
     * @return boolean True if the expense category was edited, false otherwise
     */
    public function saveNewExpenseCategory($expenseCategoriesAssignedToUser) {

        $this -> addingErrors = static::validateNewExpenseCategory($expenseCategoriesAssignedToUser, $this -> newExpenseCategory);
    
           if (empty($this -> addingErrors)) {
    
                $sql = 'INSERT INTO expenses_category_assigned_to_users (userId, name)
                        VALUES (:userId, :name)';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':userId', $this -> userId, PDO::PARAM_INT);
                $stmt -> bindValue(':name', $this -> newExpenseCategory, PDO::PARAM_STR);
    
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
     * Edit expense category
     * 
     * @return boolean True if the expense category was edited, false otherwise
     */
    public function editExpenseCategory($expenseCategoriesAssignedToUser) {

        $this -> editingErrors = static::validateNewExpenseCategory($expenseCategoriesAssignedToUser, $this -> editExpenseCategory, 
                                                            $this -> expenseCategoryAssignedToUserId);
           if (empty($this -> editingErrors)) {
    
                $sql = 'UPDATE expenses_category_assigned_to_users
                        SET name = :name
                        WHERE id = :categoryId';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':name', $this -> editExpenseCategory, PDO::PARAM_STR);
                $stmt -> bindValue(':categoryId', $this -> expenseCategoryAssignedToUserId, PDO::PARAM_INT);
                
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
     * Delete expense category
     * 
     * @return boolean True if the expense category was deleted, false otherwise
     */
    public function deleteExpenseCategory() {

        $this -> deletingErrors = static::validateNewExpenseCategory(0, 0, $this -> expenseCategoryAssignedToUserId);
                                                            
           if (empty($this -> deletingErrors)) {
    
                $sql = 'DELETE FROM expenses_category_assigned_to_users
                        WHERE id = :categoryId';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':categoryId', $this -> expenseCategoryAssignedToUserId, PDO::PARAM_INT);
                
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
      * Validate expense category
      *
      * @return array
      */
      static public function validateNewExpenseCategory($expenseCategoriesAssignedToUser, $expenseCategory,
                                                        $expenseCategoryAssignedToUserId = 0) {

        $errors = [];

        //Check if chosen category is valid
        if ($expenseCategoryAssignedToUserId != 0) {
            if ((preg_match('/^[1-9][0-9]*/', $expenseCategoryAssignedToUserId)) == 0) {
                $errors[] = 'Expense category is invalid';
            } 
        }

        if ($expenseCategory != 0) {
            //Check if variable is not empty
            if ($expenseCategory == '') {
                $errors[] = 'Expense category is required.';
            }

            //Check numbers and special characters in expense category
            if ((preg_match('/^[A-Za-z\s]+$/', $expenseCategory)) == 0) {
                $errors[] = 'Expense category must contain only letters.';
            } 

            //Check number of characters (must be less than 25)
            if (strlen($expenseCategory) > 25) {
                $errors[] = 'Expense category can not contain more than 25 letters.';
            }
        }

        if (($expenseCategory != 0) && ($expenseCategoriesAssignedToUser != 0)) {
            //Check if new expense category is unique
            if (static::checkCategoryNameExists($expenseCategoriesAssignedToUser, $expenseCategory) == true) {
                $errors[] = 'Expense category must has unique name.';
            }
        }

        return $errors;

      }

 }