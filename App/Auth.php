<?php

namespace App;

use \App\Models\User;
use \App\Models\RememberedLogin;

/**
 * Authentication
*/

class Auth {

    /**
     * Login the user
     * 
     * @param User $user The user model
     * @param Boolean $rememberMe Remember the login if true
     * 
     * @return void
     * 
     */

    public static function login($user, $rememberMe) {

        session_regenerate_id(true);

        $_SESSION['userId'] = $user -> id;

        if ($rememberMe) {

            if ($user -> rememberLogin()) {
                setcookie('rememberMe', $user -> tokenValue, $user -> expireTokenTimestamp, '/');
            }

        }
        
    }

    /**
     * Logout the user
     * 
     * @return void
     */

     public static function logout() {

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    
        session_destroy();

        static::forgetLogin();
    }

    /**
     * Remember the originally requested page in the session
     * 
     * @return void
     * 
     */
    public static function rememberRequestedPage() {

        $_SESSION['returnTo'] = $_SERVER['REQUEST_URI'];

    }

    /**
     * Get the originally requested page to
     * return to after requiring login
     * or default to the homepage
     * 
     * @return void
     * 
     */
    public static function getReturnToPage() {

        return $_SESSION['returnTo'] ?? '/';

    }

    /**
     * Get the current logged in user
     * from the session or the remember-me cookie
     * 
     * @return mixed The user model or null if not logged in
     * 
     */
    public static function getUser() {

        if (isset($_SESSION['userId'])) {

            return User::findById($_SESSION['userId']);

        } else {

            return static::loginFromRememberedCookie();

        }
    }

    /**
     * Login from remembered cookie
     * 
     * @return mixed The user model if login cookie found, null otherwise
     * 
     */
    public static function loginFromRememberedCookie() {

        $cookie = $_COOKIE['rememberMe'] ?? false;

        if ($cookie) {
            $rememberedLogin = RememberedLogin::findByToken($cookie);

                if (($rememberedLogin) && ! $rememberedLogin -> hasExpired()) {

                    $user = $rememberedLogin -> getUser();

                    static::login($user, false);

                    return $user;
                }
        }
    }

    /**
     * Forget the remembered login if present
     * 
     * @return void
     * 
     */
    public static function forgetLogin() {
        
        $cookie = $_COOKIE['rememberMe'] ?? false;

        if ($cookie) {
            $rememberedLogin = RememberedLogin::findByToken($cookie);

                if ($rememberedLogin) {

                    $rememberedLogin -> delete();
                }

                setcookie('rememberMe', '', time() - 3600, '/');
        }
    }

 }