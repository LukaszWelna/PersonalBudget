<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Password controller
 */

class Password extends \Core\Controller {

    /**
     * Class constructor
     * 
     * @param array $route_params Route parameters
     * 
     * @return void
     * 
     */
  public function __construct($route_params) {

    parent::__construct($route_params);

    $_SESSION["page"] = 'Password';

  }

    /**
     * Show the forgotten password page
     * 
     * @return void
     */
     public function forgotAction() {

        View::renderTemplate('Password/forgot.html');

     }

    /**
     * Send the forgotten reset link to the supplied email
     * 
     * @return void
     */
    public function requestResetAction() {

        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/resetRequested.html');

     }

     /**
      * Show the reset password form
      *
      * @return void
      */
      public function resetAction() {

        $token = $this -> route_params['token'];

        $user = $this -> getUserOrExit($token);

        View::renderTemplate('Password/reset.html' , [
            'token' => $token
        ]);

      }

     /**
      * Reset the user's password
      *
      * @return void
      */
      public function resetPasswordAction() {

        $token = $_POST['token'];

        $user = $this -> getUserOrExit($token);

        if ($user -> resetPassword($_POST['password'])) {

            View::renderTemplate('Password/resetSuccess.html');

        } else {

            View::renderTemplate('Password/reset.html' , [
                'token' => $token,
                'user' => $user
            ]);

        }
      }

      /**
       * Find the user model associated with the password reset token or end the request with a message
       * 
       * @param string $token Password reset token to the user
       * 
       * @return mixed User object if found and the token has not expired, null otherwise
       * 
       */
      protected function getUserOrExit($token) {

        $user = User::findByPasswordReset($token);

        if ($user) {

            return $user;

        } else {

            View::renderTemplate('Password/tokenExpired.html');
            exit;
            
        }
        }

 }