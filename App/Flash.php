<?php

namespace App;

/**
 * Flash notification messages: messages for one-time display using the session
 * for storage between requests
 */

class Flash {

    /**
     * Success message type
     * @var string
     * 
     */
    const SUCCESS = 'success';

    /**
     * Information message type
     * @var string
     * 
     */
    const INFO = 'info';

    /**
     * Warning message type
     * @var string
     * 
     */
    const WARNING = 'warning';

    /**
     * Add a message
     * 
     * @param string $message Message content
     * @param string $type Message type
     * @return void
     * 
     */

    public static function addMessage($message, $type = 'success') {

        if (! isset($_SESSION['flashNotifications'])) {
            $_SESSION['flashNotifications'] = [];
        }

        $_SESSION['flashNotifications'][] = [
            'body' => $message,
            'type' => $type
        ];
    }

    /**
     * Get all the messages
     * 
     * @return mixed An array with messages or null if none set
     * 
     */

     public static function getMessages() {
        
        if (isset($_SESSION['flashNotifications'])) {
            $messages = $_SESSION['flashNotifications'];
            unset($_SESSION['flashNotifications']);
            return $messages;
        }   
    }

 }