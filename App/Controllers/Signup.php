<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Signup controller
 */

class Signup extends \Core\Controller {

    /**
     * Class constructor
     * 
     * @return void
     * 
     */
  public function __construct($route_params) {

    parent::__construct($route_params);

    $_SESSION["page"] = 'Signup';

  }

    /**
     * Sign up a new user
     * 
     * @return void
     */
    public function createAction() {
        
        $user = new User($_POST);

        if ($user -> save()) {

            User::setDefaultCategoriesToUser();

            $user -> sendActivationEmail();
            
            $this -> redirect('/signup/success');

        } else {
            
            View::renderTemplate('Home/index.html', [
                'user' => $user
            ]);
            
        }
    }
     
    /**
     * Show the success page
     * 
     * @return void
     */
    public function successAction() {

        View::renderTemplate('Signup/success.html');

     }

     /**
      * Activate a new account
      *
      * @return void
      */
      public function activateAction() {

        $token = $this -> route_params['token'];

        User::activate($token);

        $this -> redirect('/signup/activated');

      }

    /**
      * Show the activation success page
      *
      * @return void
      */
      public function activatedAction() {

        View::renderTemplate('Signup/activated.html');

      }
 }