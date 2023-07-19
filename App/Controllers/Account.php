<?php

namespace App\Controllers;

use App\Models\User;

/**
 * Account controller
 */

 class Account extends \Core\Controller {

    /**
     * Validate if email is available (AJAX) for a new signup
     * 
     * @return void
     * 
     */
    public function validateEmailAction() {

        $isValid = ! User::emailExists($_GET['email'], $_GET['ignoreId'] ?? null);

        header('Content-Type: application/json');

        echo json_encode($isValid);
        
    }
 }