<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Date;
use \App\Balance AS AppBalance;
use \App\Models\Income;
use \App\Models\Expense;

/**
 * Balance controller
 */

 class Balance extends Authenticated {

    /**
     * User id
     * @var Integer
     * 
     */
    public $loggedUserId;

    /**
     * Class constructor
     * 
     * @return void
     */
    public function __construct() {

        $_SESSION["page"] = 'Balance';

        $this -> loggedUserId = Auth::getUser() -> id;

    }

    /**
     * Show the balance page
     * 
     * @return void
     * 
     */
    public function showAction() {

        View::renderTemplate('Balance/show.html');

    }

     /**
     * Show the results on balance page
     * 
     * @return void
     * 
     */
    public function showResultsAction() {

        // Get start and end date
        $date = new Date($_POST);

        if ($date -> getDateRange()) {

            $balance = new AppBalance($this -> loggedUserId, $date -> dates);

            View::renderTemplate('Balance/show.html', [
                'selectedDate' => $_POST,
                'userIncomes' => $balance -> userIncomes,
                'groupedUserIncomes' => $balance -> groupedUserIncomes,
                'totalAmountOfUserIncomes' => $balance -> totalAmountOfUserIncomes,
                'userExpenses' => $balance -> userExpenses,
                'groupedUserExpenses' => $balance -> groupedUserExpenses,
                'totalAmountOfUserExpenses' => $balance -> totalAmountOfUserExpenses,
                'balance' => $balance -> balance,
            ]);
            
        } else {

            Flash::addMessage('Failed to show balance', 'warning');

            View::renderTemplate('Balance/show.html', [
                'selectedDate' => $_POST,
                'startDate' => $date -> dates['start'],
                'endDate' => $date -> dates['end'],
                'date' => $date
            ]);
            
        }

    }

 }

 