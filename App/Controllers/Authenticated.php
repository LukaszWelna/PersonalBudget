<?php

namespace App\Controllers;

/**
 * Authenticated base controller
 */

 abstract class Authenticated extends \Core\Controller {

    /**
     * Before filter - called before an action method
     * Require the user to be authenticated before giving access to all methods in the controller
     * 
     * @return void
     */

     protected function before() {

        $this -> requireLogin();
        
     }

 }