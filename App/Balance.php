<?php

namespace App;

use \App\Models\Income;
use \App\Models\Expense;

/**
 * Balance
 */

class Balance {

    /**
     * User incomes
     * @var array
     */
    public $userIncomes = [];

    /**
     * Grouped user incomes
     * @var array
     */
    public $groupedUserIncomes = [];

    /**
     * Total amount of user incomes
     * @var double
     */
    public $totalAmountOfUserIncomes;

     /**
     * User expenses
     * @var array
     */
    public $userExpenses = [];

    /**
     * Grouped user expenses
     * @var array
     */
    public $groupedUserExpenses = [];

    /**
     * Total amount of user expenses
     * @var double
     */
    public $totalAmountOfUserExpenses;

    /**
     * Logged user id
     * @var int
     */
    public $loggedUserId;

    /**
     * Range of dates
     * @var array
     */
    public $dates = [];

    /**
     * Difference between incomes and expenses (balance)
     * @var double
     */
    public $balance;

     public function __construct($loggedUserId, $dates) {

        $this -> loggedUserId = $loggedUserId;
        $this -> dates = $dates;

        $this -> getIncomes();
        $this -> getExpenses();
        $this -> getBalance();

     }

     /**
      * Get incomes data
      *
      * @return void
      */
     public function getIncomes() {

         $this -> userIncomes = Income::getAll($this -> loggedUserId, $this -> dates);
         $this -> groupedUserIncomes = Income::getGroupedUserIncomes($this -> loggedUserId,$this -> dates);
         $this -> totalAmountOfUserIncomes = Income::getTotalAmount($this -> loggedUserId, $this -> dates);

     }

    /**
      * Get expenses data
      *
      * @return void
      */
      public function getExpenses() {

        $this -> userExpenses = Expense::getAll($this -> loggedUserId, $this -> dates);
        $this -> groupedUserExpenses = Expense::getGroupedUserExpenses($this -> loggedUserId, $this -> dates);
        $this -> totalAmountOfUserExpenses = Expense::getTotalAmount($this -> loggedUserId, $this -> dates);

    }

     /**
      * Get difference between incomes and expenses (balance)
      *
      * @return void
      */
      public function getBalance() {

        $this -> balance = $this -> totalAmountOfUserIncomes - $this ->totalAmountOfUserExpenses;

    }

 }