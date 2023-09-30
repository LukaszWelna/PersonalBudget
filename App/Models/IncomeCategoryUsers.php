<?php

namespace App\Models;

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
     * New income category
     * @var string
     */
    public $newIncomeCategory;

    /**
     * Edit income category
     * @var string
     */
    public $editIncomeCategory;

    /**
     * Income category assigned to user id 
     * @var int
     */
    public $incomeCategoryAssignedToUserId;

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
     * Deleting income error messages 
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

        $sql = 'SELECT id, name FROM incomes_category_assigned_to_users WHERE userId = :loggedUserId';

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

     static public function checkCategoryNameExists($incomeCategories, $newCategoryName, $incomeCategoryAssignedToUserId) {

        //Check if new income category is unique
        foreach ($incomeCategories as $category) {
            if ((strtoupper($newCategoryName) == strtoupper($category -> name)) && ($incomeCategoryAssignedToUserId != $category -> id)) {
                return true;
            }
        }
        return false;
     }

      /**
     * Save new income category
     * 
     * @return boolean True if the income category was saved, false otherwise
     */
    public function saveNewIncomeCategory($incomeCategoriesAssignedToUser) {

        $this -> addingErrors = static::validateNewIncomeCategory($incomeCategoriesAssignedToUser, $this -> newIncomeCategory);
    
           if (empty($this -> addingErrors)) {
    
                $sql = 'INSERT INTO incomes_category_assigned_to_users (userId, name)
                        VALUES (:userId, :name)';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':userId', $this -> userId, PDO::PARAM_INT);
                $stmt -> bindValue(':name', $this -> newIncomeCategory, PDO::PARAM_STR);
    
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
     * Edit income category
     * 
     * @return boolean True if the income category was edited, false otherwise
     */
    public function editIncomeCategory($incomeCategoriesAssignedToUser) {

        $this -> editingErrors = static::validateNewIncomeCategory($incomeCategoriesAssignedToUser, $this -> editIncomeCategory, 
                                                            $this -> incomeCategoryAssignedToUserId);
           if (empty($this -> editingErrors)) {
    
                $sql = 'UPDATE incomes_category_assigned_to_users
                        SET name = :name
                        WHERE id = :categoryId';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':name', $this -> editIncomeCategory, PDO::PARAM_STR);
                $stmt -> bindValue(':categoryId', $this -> incomeCategoryAssignedToUserId, PDO::PARAM_INT);
                
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
     * Delete income category
     * 
     * @return boolean True if the income category was deleted, false otherwise
     */
    public function deleteIncomeCategory() {

        $this -> deletingErrors = static::validateNewIncomeCategory(0, 0, $this -> incomeCategoryAssignedToUserId);
                                                            
           if (empty($this -> deletingErrors)) {
    
                $sql = 'DELETE FROM incomes_category_assigned_to_users
                        WHERE id = :categoryId';
    
                $db = static::getDB();
                $stmt = $db -> prepare($sql);
                $stmt -> bindValue(':categoryId', $this -> incomeCategoryAssignedToUserId, PDO::PARAM_INT);
                
                return $stmt -> execute();
    
            }
    
            return false;
            
         }

    /**
      * Validate income category
      *
      * @return array
      */
      static public function validateNewIncomeCategory($incomeCategoriesAssignedToUser, $incomeCategory, 
                                                        $incomeCategoryAssignedToUserId = 0) {

        $errors = [];

        //Check if chosen category is valid
        if ($incomeCategoryAssignedToUserId != 0) {
            if ((preg_match('/^[1-9][0-9]*/', $incomeCategoryAssignedToUserId)) == 0) {
                $errors[] = 'Income category is invalid';
            } 
        }

        if ($incomeCategory != 0) {
            //Check if variable is not empty
            if ($incomeCategory == '') {
                $errors[] = 'Income category is required.';
            }

            //Check numbers and special characters in income category
            if ((preg_match('/^[A-Za-z\s]+$/', $incomeCategory)) == 0) {
                $errors[] = 'Income category must contain only letters.';
            } 

            //Check number of characters (must be less than 25)
            if (strlen($incomeCategory) > 25) {
                $errors[] = 'Income category can not contain more than 25 letters.';
            }
        }
        
        if (($incomeCategory != 0) && ($incomeCategoriesAssignedToUser != 0)) {
            //Check if new income category is unique
            if (static::checkCategoryNameExists($incomeCategoriesAssignedToUser, $incomeCategory, $incomeCategoryAssignedToUserId) == true) {
                $errors[] = 'Income category must has unique name.';
            }
        }

        return $errors;
      }

 }