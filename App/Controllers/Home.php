<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Home controller
 */

 class Home extends \Core\Controller {

   /**
    * Class constructor
    *
    * @return void
    */

   public function __construct() {

      $_SESSION["page"] = 'Home';

  }

    /**
     * Show the index page
     * 
     * @return void
     */
     public function indexAction() {

        View::renderTemplate('Home/index.html');

     }

 }