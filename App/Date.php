<?php

namespace App;

/**
 * Manage dates
 */

class Date {

    /**
     * Get current date
     * 
     * @return string Current date
     * 
     */

    public static function getCurrentDate() {

        $date = new \DateTime();
        return $date -> format('Y-m-d');
        
    }

 }