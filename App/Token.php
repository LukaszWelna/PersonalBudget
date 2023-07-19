<?php

namespace App;

/**
 * Unique random keys
 */

class Token {

    /**
     * The token value
     * @var array
     * 
     */
    protected $token;

    /**
     * Class constructor
     * Generate new token or assign an existing one if passed in
     * 
     * @param string Token value
     * 
     * @return void
     */
    public function __construct($tokenValue = null) {

        if ($tokenValue) {

            $this -> token = $tokenValue;

        } else {

            $this -> token = bin2hex(random_bytes(16));
            
        }
    }

    /**
     * Get the token value
     * 
     * @return string The token value
     * 
     */
    public function getTokenValue() {

        return $this -> token; 

    }

    /**
     * Get the hashed token value
     * 
     * @return string The hashed token value
     * 
     */
    public function getHashedTokenValue() {

        return hash_hmac('sha256', $this -> token, \App\Config::SECRET_KEY);
        
    }

 }