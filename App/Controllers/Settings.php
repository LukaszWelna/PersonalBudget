<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\IncomeCategoryUsers;
use \App\Models\ExpenseCategoryUsers;
use \App\Models\PaymentMethodUsers;
use \App\Models\Income as IncomeModel;
use \App\Models\Expense as ExpenseModel;

/**
 * Account controller
 */

 class Settings extends Authenticated {

    /**
     * @var user User object returned from Auth class
     * 
     */
    public $user;

    /**
     * Income categories assigned to logged user
     * @var array
     * 
     */
    public $incomeCategories;

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
     * Before filter - called before an action method
     * 
     * @return void
     */

     protected function before() {

        parent::before();

        $this -> user = Auth::getUser();

        $this -> incomeCategories = IncomeCategoryUsers::getAll($this -> user -> id);

        $this -> expenseCategories = ExpenseCategoryUsers::getAll($this -> user -> id);
        
        $this -> paymentMethods = PaymentMethodUsers::getAll($this -> user -> id);
     }

    /**
    * Class constructor
    *
    * @return void
    */

   public function __construct() {

    $_SESSION["page"] = 'Settings';

}

    /**
     * Show the settings
     * 
     * @return void
     * 
     */
    public function showAction() {

        View::renderTemplate('Settings/show.html', [
            'user' => $this -> user,
            'currentIncomeCategories' => $this -> incomeCategories,
            'currentExpenseCategories' => $this -> expenseCategories,
            'currentPaymentMethods' => $this -> paymentMethods
        ]);
    }

    /**
     * Show the form to edit the profile
     * 
     * @return void
     * 
     */
    public function editAction() {

        View::renderTemplate('Settings/edit.html', [
            'user' => $this -> user
        ]);
    }

    /**
     * Update the profile
     * 
     * @return void
     * 
     */
    public function updateAction() {

        if ($this -> user -> updateProfile($_POST)) {

            Flash::addMessage('Changes saved');

            $this -> redirect('/settings/show');

        } else {

            View::renderTemplate('Settings/edit.html', [
                'user' => $this -> user
            ]);

        }
    }

    /**
     * Delete the account
     * 
     * @return void
     * 
     */
    public function deleteAction() {

        if ($this -> user -> deleteUser($_POST)) {

            Flash::addMessage('Account deleted successfully');

            $this -> redirect('/');

        } else {

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);

        }
    }

    /**
     * Add income category
     * 
     * @return void
     * 
     */
    public function addIncomeCategoryAction() {

        $incomeCategoryUsers = new IncomeCategoryUsers($this -> user -> id, $_POST);

        if ($incomeCategoryUsers -> saveNewIncomeCategory($this -> incomeCategories)) {

            $this -> redirect('/settings/incomeCategoryAddedMessage');

        } else {

            Flash::addMessage('Failed to add income category', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'incomeCategoryUsers' => $incomeCategoryUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after adding the income category
      *
      * @return void
      */
      public function incomeCategoryAddedMessageAction() {

        Flash::addMessage('Income category added');

        $this -> redirect('/Settings/show');

      }

    /**
     * Edit income category
     * 
     * @return void
     * 
     */
    public function editIncomeCategoryAction() {

        $incomeCategoryUsers = new IncomeCategoryUsers($this -> user -> id, $_POST);

        if ($incomeCategoryUsers -> editIncomeCategory($this -> incomeCategories)) {

            $this -> redirect('/settings/incomeCategoryEditedMessage');

        } else {

            Flash::addMessage('Failed to edit income category', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'incomeCategoryUsers' => $incomeCategoryUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after editing the income category
      *
      * @return void
      */
      public function incomeCategoryEditedMessageAction() {

        Flash::addMessage('Income category edited');

        $this -> redirect('/Settings/show');

      }

    /**
     * Delete income category
     * 
     * @return void
     * 
     */
    public function deleteIncomeCategoryAction() {

        $incomeCategoryUsers = new IncomeCategoryUsers($this -> user -> id, $_POST);

        if ($incomeCategoryUsers -> deleteIncomeCategory()) {

            $this -> redirect('/settings/incomeCategoryDeletedMessage');

        } else {

            Flash::addMessage('Failed to delete income category', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'incomeCategoryUsers' => $incomeCategoryUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after deleting the income category
      *
      * @return void
      */
      public function incomeCategoryDeletedMessageAction() {

        Flash::addMessage('Income category deleted');

        $this -> redirect('/Settings/show');

      }

    /**
     * Validate if new income category is available (AJAX)
     * 
     * @return void
     * 
     */
    public function validateNewIncomeCategoryAction() {
        
        if (isset($_GET['newIncomeCategory'])) {
            $incomeCategory = $_GET['newIncomeCategory'];
        } else if (isset($_GET['editIncomeCategory'])) {
            $incomeCategory = $_GET['editIncomeCategory'];
        } 

        $isValid = ! IncomeCategoryUsers::checkCategoryNameExists($this -> incomeCategories, $incomeCategory);

        header('Content-Type: application/json');

        echo json_encode($isValid);
        
    }

     /**
     * Add expense category
     * 
     * @return void
     * 
     */
    public function addExpenseCategoryAction() {

        $expenseCategoryUsers = new ExpenseCategoryUsers($this -> user -> id, $_POST);

        if ($expenseCategoryUsers -> saveNewExpenseCategory($this -> expenseCategories)) {

            $this -> redirect('/settings/expenseCategoryAddedMessage');

        } else {

            Flash::addMessage('Failed to add expense category', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'expenseCategoryUsers' => $expenseCategoryUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after adding the expense category
      *
      * @return void
      */
      public function expenseCategoryAddedMessageAction() {

        Flash::addMessage('Expense category added');

        $this -> redirect('/Settings/show');

      }

    /**
     * Edit expense category
     * 
     * @return void
     * 
     */
    public function editExpenseCategoryAction() {

        $expenseCategoryUsers = new ExpenseCategoryUsers($this -> user -> id, $_POST);

        if ($expenseCategoryUsers -> editExpenseCategory($this -> expenseCategories)) {

            $this -> redirect('/settings/expenseCategoryEditedMessage');

        } else {

            Flash::addMessage('Failed to edit expense category', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'expenseCategoryUsers' => $expenseCategoryUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after editing the expense category
      *
      * @return void
      */
      public function expenseCategoryEditedMessageAction() {

        Flash::addMessage('Expense category edited');

        $this -> redirect('/Settings/show');

      }

    /**
     * Delete expense category
     * 
     * @return void
     * 
     */
    public function deleteExpenseCategoryAction() {

        $expenseCategoryUsers = new ExpenseCategoryUsers($this -> user -> id, $_POST);

        if ($expenseCategoryUsers -> deleteExpenseCategory()) {

            $this -> redirect('/settings/expenseCategoryDeletedMessage');

        } else {

            Flash::addMessage('Failed to delete expense category', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'expenseCategoryUsers' => $expenseCategoryUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after deleting the expense category
      *
      * @return void
      */
      public function expenseCategoryDeletedMessageAction() {

        Flash::addMessage('Expense category deleted');

        $this -> redirect('/Settings/show');

      }

    /**
     * Validate if new expense category is available (AJAX)
     * 
     * @return void
     * 
     */
    public function validateNewExpenseCategoryAction() {

        if (isset($_GET['newExpenseCategory'])) {
            $expenseCategory = $_GET['newExpenseCategory'];
        } else if (isset($_GET['editExpenseCategory'])) {
            $expenseCategory = $_GET['editExpenseCategory'];
        }

        $isValid = ! ExpenseCategoryUsers::checkCategoryNameExists($this -> expenseCategories, $expenseCategory);

        header('Content-Type: application/json');

        echo json_encode($isValid);
        
    }

    /**
     * Add payment method
     * 
     * @return void
     * 
     */
    public function addPaymentMethodAction() {

        $paymentMethodUsers = new PaymentMethodUsers($this -> user -> id, $_POST);

        if ($paymentMethodUsers -> saveNewPaymentMethod($this -> paymentMethods)) {

            $this -> redirect('/settings/paymentMethodAddedMessage');

        } else {

            Flash::addMessage('Failed to add payment method', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'paymentMethodUsers' => $paymentMethodUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after adding the payment method
      *
      * @return void
      */
      public function paymentMethodAddedMessageAction() {

        Flash::addMessage('Payment method added');

        $this -> redirect('/Settings/show');

      }

    /**
     * Edit payment method
     * 
     * @return void
     * 
     */
    public function editPaymentMethodAction() {

        $paymentMethodUsers = new PaymentMethodUsers($this -> user -> id, $_POST);

        if ($paymentMethodUsers -> editPaymentMethod($this -> paymentMethods)) {

            $this -> redirect('/settings/paymentMethodEditedMessage');

        } else {

            Flash::addMessage('Failed to edit payment method', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'paymentMethodUsers' => $paymentMethodUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after editing the payment method
      *
      * @return void
      */
      public function paymentMethodEditedMessageAction() {

        Flash::addMessage('Payment method edited');

        $this -> redirect('/Settings/show');

      }

      /**
     * Delete payment method
     * 
     * @return void
     * 
     */
    public function deletePaymentMethodAction() {

        $paymentMethodUsers = new PaymentMethodUsers($this -> user -> id, $_POST);

        if ($paymentMethodUsers -> deletePaymentMethod()) {

            $this -> redirect('/settings/paymentMethodDeletedMessage');

        } else {

            Flash::addMessage('Failed to delete payment method', 'warning');

            View::renderTemplate('Settings/show.html', [
                'user' => $this -> user,
                'paymentMethodUsers' => $paymentMethodUsers,
                'currentIncomeCategories' => $this -> incomeCategories,
                'currentExpenseCategories' => $this -> expenseCategories,
                'currentPaymentMethods' => $this -> paymentMethods
            ]);
            
        }
    }

     /**
      * Show successful message after deleting the payment method
      *
      * @return void
      */
      public function paymentMethodDeletedMessageAction() {

        Flash::addMessage('Payment method deleted');

        $this -> redirect('/Settings/show');

      }

    /**
     * Validate if new expense category is available (AJAX)
     * 
     * @return void
     * 
     */
    public function validateNewPaymentMethodAction() {

        if (isset($_GET['newPaymentMethod'])) {
            $paymentMethod = $_GET['newPaymentMethod'];
        } else if (isset($_GET['editPaymentMethod'])) {
            $paymentMethod = $_GET['editPaymentMethod'];
        }

        $isValid = ! PaymentMethodUsers::checkPaymentMethodNameExists($this -> paymentMethods, $paymentMethod);

        header('Content-Type: application/json');

        echo json_encode($isValid);
        
    }

 }