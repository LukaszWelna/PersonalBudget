<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

/**
 * Account controller
 */

 class Profile extends Authenticated {

    /**
     * @var user User object returned from Auth class
     * 
     */
    public $user;

     /**
     * Before filter - called before an action method
     * 
     * @return void
     */

     protected function before() {

        parent::before();

        $this -> user = Auth::getUser();
        
     }

    /**
    * Class constructor
    *
    * @return void
    */

   public function __construct() {

    $_SESSION["page"] = 'Profile';

}

    /**
     * Show the profile
     * 
     * @return void
     * 
     */
    public function showAction() {

        View::renderTemplate('Profile/show.html', [
            'user' => $this -> user
        ]);
    }

    /**
     * Show the form to edit the profile
     * 
     * @return void
     * 
     */
    public function editAction() {

        View::renderTemplate('Profile/edit.html', [
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

            $this -> redirect('/profile/show');

        } else {

            View::renderTemplate('Profile/edit.html', [
                'user' => $this -> user
            ]);

        }
       
    }

 }