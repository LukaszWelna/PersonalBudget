<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;


/**
 * Login controller
 */

class Login extends \Core\Controller {

    /**
     * Log in
     * 
     * @return void
     */
    public function createAction() {
       
       $user = User::authenticate($_POST['email'], $_POST['password']);

       $rememberMe = isset($_POST['rememberMe']);

       if ($user) {

        Auth::login($user, $rememberMe);

        $this -> redirect(Auth::getReturnToPage());

       } else {

        Flash::addMessage('Login unsuccessful, please try again', Flash::WARNING);
       
        View::renderTemplate('Home/index.html', [
            'email' => $_POST['email'],  
            'rememberMe' => $rememberMe
        ]);

       }

     }

     /**
      * Log out a user
      * @return void
      */
      public function destroyAction() {
        Auth::logout();

        $this -> redirect('/login/showLogoutMessage');
      }

      /**
      * Show logout message
      * @return void
      */
      public function showLogoutMessageAction() {
       
        Flash::addMessage('Logout successful');

        $this -> redirect('/');
      }
 }