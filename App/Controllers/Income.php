<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Income as IncomeModel;
use \App\Date;
use \App\Models\IncomeCategoryUsers;

/**
 * Income controller
 */

 class Income extends Authenticated {

    /**
     * User id
     * @var Integer
     * 
     */
    public $loggedUserId;

    /**
     * Income categories assigned to logged user
     * @var array
     * 
     */
    public $incomeCategories;


    /**
     * Class constructor
     * 
     * @return void
     */
    public function __construct() {

        $_SESSION["page"] = 'Income';

        $this -> loggedUserId = Auth::getUser() -> id;

        $this -> incomeCategories = IncomeCategoryUsers::getAll($this -> loggedUserId);

    }

    /**
     * Show the income form
     * 
     * @return void
     * 
     */
    public function showAction() {

        View::renderTemplate('Income/show.html', [
            'incomeCategories' => $this -> incomeCategories
        ]);

    }

    /**
     * Add the income
     * 
     * @return void
     * 
     */
    public function addAction() {

        $income = new IncomeModel($this -> loggedUserId, $_POST);

        if ($income -> save()) {

            $this -> redirect('/income/incomeAddedMessage');

        } else {

            Flash::addMessage('Failed to add the income', 'warning');

            View::renderTemplate('Income/show.html', [
                'income' => $income,
                'incomeCategories' => $this -> incomeCategories
            ]);
            
        }
    }

      /**
      * Show successful message after adding the income
      * @return void
      */
      public function incomeAddedMessageAction() {

        Flash::addMessage('Income added');

        $this -> redirect('/income/show');

      }



 }

 