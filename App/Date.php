<?php

namespace App;

/**
 * Manage dates
 */

class Date {

     /**
     * Post array with option chosen by user
     * @var array
     */
    public $chosenDateOption = [];

    /**
     * Range of dates
     * @var array
     */
    public $dates = [];

    /**
     * Date validation errors
     * @var array
     */
    public $errors = [];

    /**
     * Get current date
     * 
     * @return string Current date
     * 
     */

     public function __construct($postArray) {

        $this -> chosenDateOption = $postArray;

     }

     /**
      * Get current date
      *
      * @return string $date Current date in string format
      *
      */
    public static function getCurrentDate() {

        $date = new \DateTime();
        return $date -> format('Y-m-d');
        
    }

    /**
     * Get range of dates base on form
     * 
     * @return boolean True if validation not needed or passed, false otherwise
     * 
     */

     public function getDateRange() {

        if (isset($this -> chosenDateOption['choosePeriod'])) {

            if ($this -> chosenDateOption['choosePeriod'] == 1) {

                $this -> getCurrentMonthDates();
    
            } else if ($this -> chosenDateOption['choosePeriod'] == 2) {
    
                $this -> getPreviousMonthDates();
    
            } else if ($this -> chosenDateOption['choosePeriod'] == 3) {
    
                $this -> getCurrentYearDates();
    
            } else {

                $this -> dates['start'] = static::getCurrentDate();
                $this -> dates['end'] = static::getCurrentDate();

                return false;

            }

        } else {

            $this -> dates['start'] = $this -> chosenDateOption['startDate'];
            $this -> dates['end'] = $this -> chosenDateOption['endDate'];

            $this -> validate();

            if (empty($this -> errors)) {

                return true;

            } else {

                return false;

            }
        }

        return true;
    }

    /**
     * Date validation
     * 
     * @return array Range of current year dates
     * 
     */
    public function validate() {

        // start date
        if ($this -> dates['start'] != "") {
        $dateAndTime = date_create_from_format('Y-m-d', $this -> dates['start']);
        
            if ($dateAndTime === false) {
                $this -> errors[] = "Invalid start date";
            }
            else {
                $dateErrors = date_get_last_errors();

                if ($dateErrors != NULL && $dateErrors["warning_count"]) {
                    $this -> errors[] = "Invalid start date";
                }
            }

            if (strtotime($this -> dates['start']) < strtotime('2000-01-01')) {
                $this -> errors[] = "Start date must be equal or greather than 01-01-2000";
            }
    
            if (strtotime($this -> dates['start']) > strtotime(Date::getCurrentDate())) {
                $this -> errors[] = "Start date must be equal or earlier than current date";
            }

        } else {
            $this -> errors[] = "Start date is required";
        }

         // end date
         if ($this -> dates['end'] != "") {
            $dateAndTime = date_create_from_format('Y-m-d', $this -> dates['end']);
            
                if ($dateAndTime === false) {
                    $this -> errors[] = "Invalid endDate date";
                } else {
                    $dateErrors = date_get_last_errors();
        
                    if ($dateErrors != NULL && $dateErrors["warning_count"]) {
                        $this -> errors[] = "Invalid endDate date";
                    }
                }

                if (strtotime($this -> dates['end']) < strtotime('2000-01-01')) {
                    $this -> errors[] = "End date must be equal or greather than 01-01-2000";
                }
        
                if (strtotime($this -> dates['end']) > strtotime(Date::getCurrentDate())) {
                    $this -> errors[] = "End date must be equal or earlier than current date";
                }

            } else {
                $this -> errors[] = "End date is required";
            }

        }

    /**
     * Get current month range
     * 
     * @return void
     * 
     */
    public function getCurrentMonthDates() {

        $date = new \DateTime();
        $this -> dates['start'] = $date -> format('Y-m-01');
        $this -> dates['end'] = $date -> format('Y-m-d');

    }

    /**
     * Get previous month range
     * 
     * @return void
     */
    public function getPreviousMonthDates() {

        $dateStart = new \DateTime("first day of last month");
        $dateEnd = new \DateTime("last day of last month");

        $this -> dates['start'] = $dateStart -> format('Y-m-01');
        $this -> dates['end'] = $dateEnd -> format('Y-m-d');

    }

    /**
     * Get current year range
     * 
     * @return void
     */
    public function getCurrentYearDates() {

        $date = new \DateTime();
        $this -> dates['start'] = $date -> format('Y-01-01');
        $this -> dates['end'] = $date -> format('Y-m-d');
        
    }

    /**
     * Get given month range
     * 
     * @param string $date Chosen date by user
     * 
     * @return array Given month start and end dates
     */
    public static function getGivenMonthDates($date) {

        $dates =[];

        $dates['start'] = date('Y-m-01', strtotime($date));
        $dates['end'] = date('Y-m-t', strtotime($date));

        return $dates;

    }

 }