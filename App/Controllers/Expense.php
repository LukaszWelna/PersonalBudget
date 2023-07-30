<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Expense as ExpenseModel;
use \App\Date;
use \App\Models\ExpenseCategoryUsers;
use \App\Models\PaymentMethodUsers;

/**
 * Account controller
 */

 class Expense extends Authenticated {

    /**
     * User id
     * @var Integer
     * 
     */
    public $loggedUserId;

    /**
     * Expense categories assigned to logged user
     * @var array
     * 
     */
    public $expenseCategories;

    /**
     * Payment methods assigned to logged user
     * @var array
     * 
     */
    public $paymentMethods;


    /**
     * Class constructor
     * 
     * @return void
     */
    public function __construct() {

        $_SESSION["page"] = 'Expense';

        $this -> loggedUserId = Auth::getUser() -> id;

        $this -> expenseCategories = ExpenseCategoryUsers::getAll($this -> loggedUserId);

        $this -> paymentMethods = PaymentMethodUsers::getAll($this -> loggedUserId);

    }

    /**
     * Show the expense form
     * 
     * @return void
     * 
     */
    public function showAction() {

        View::renderTemplate('Expense/show.html', [
            'expenseCategories' => $this -> expenseCategories,
            'paymentMethods' => $this -> paymentMethods
        ]);

    }

    /**
     * Add the expense
     * 
     * @return void
     * 
     */
    public function addAction() {

        $expense = new ExpenseModel($this -> loggedUserId, $_POST);

        if ($expense -> save()) {

            $this -> redirect('/expense/expenseAddedMessage');

        } else {

            Flash::addMessage('Failed to add the expense', 'warning');

            View::renderTemplate('Expense/show.html', [
                'expense' => $expense,
                'expenseCategories' => $this -> expenseCategories,
                'paymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

      /**
      * Show successful message after adding the epense
      * @return void
      */
      public function expenseAddedMessageAction() {

        Flash::addMessage('Expense added');

        $this -> redirect('/expense/show', [
            'expenseCategories' => $this -> expenseCategories,
            'paymentMethods' => $this -> paymentMethods
        ]);

      }

 }

 