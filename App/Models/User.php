<?php

namespace App\Models;

use \App\Token;
use \App\Mail;
use \Core\View;

use PDO;

/**
 * User model
 */

 class User extends \Core\Model {

    /**
     * User id
     * @var int
     */
    public $id;

    /**
     * User name
     * @var string
     */
     public $name;

    /**
     * User hashed password
     * @var string
     */
    public $passwordHash;

    /**
     * User email
     * @var string
     */
     public $email;

    /**
     * User repeated password
     * @var string
     */
    public $passwordConfirmation;

    /**
     * User password
     * @var string
     */
    public $password;

    /**
     * Error messages
     * @var array
     */
    public $errors = [];

    /**
     * User token value
     * @var string
     */
    public $tokenValue;

    /**
     * User token expiry date
     * @var string
     */
    public $expireTokenTimestamp;

    /**
     * User password reset hash
     * @var string
     */
    public $passwordResetHash;

    /**
     * User password reset hash expiry date
     * @var string
     */
    public $passwordResetExpiresAt;

    /**
     * User password reset token value
     * @var string
     */
    public $passwordResetTokenValue;

    /**
     * User account activation token
     * @var string
     */
    public $activationTokenValue;

    /**
     * User account activation token hash
     * @var string
     */
    public $activationHash;

    /**
     * User account active flag
     * @var boolean
     */
    public $isActive;

    /**
     * Class constructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     * 
     */
    public function __construct($data = []) {

        foreach ($data as $key => $value) {
            $this -> $key = $value;
        }

    }

    /**
     * Save the user model with the current property values
     * 
     * @return boolean True if the user was saved, false otherwise
     */
     public function save() {

        $this -> validate();

        if (empty($this -> errors)) {

            $passwordHash = password_hash($this -> password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashedToken = $token -> getHashedTokenValue();
            $this -> activationTokenValue = $token -> getTokenValue();

            $sql = 'INSERT INTO users (name, email, passwordHash, activationHash)
                    VALUES (:name, :email, :passwordHash, :activationHash)';

            $db = static::getDB();
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':name', $this -> name, PDO::PARAM_STR);
            $stmt -> bindValue(':email', $this -> email, PDO::PARAM_STR);
            $stmt -> bindValue(':passwordHash', $passwordHash, PDO::PARAM_STR);
            $stmt -> bindValue(':activationHash', $hashedToken, PDO::PARAM_STR);

            return $stmt -> execute();

        }

        return false;
        
     }
     
     /**
      * Validate current property values, adding validation error messages to the errors array property 
      *
      * @return void
      */
      public function validate() {
        // Name 
        if ($this -> name == '') {
            $this -> errors[] = 'Name is required';
        }

        // Email address 
        if (filter_var($this -> email, FILTER_VALIDATE_EMAIL) === false) {
            $this -> errors[] = 'Invalid email';
        }

        if (static::emailExists($this -> email, $this -> id ?? null)) {
            $this -> errors[] = 'Email already exists in database';
        }

        // Password 
        if (isset($this -> password)) {
            if (strlen($this -> password) < 6) {
                $this -> errors[] = 'Please enter at least 6 characters fot the password';
            }

            if ((preg_match('/.*[a-z]+.*/i', $this -> password)) == 0) {
                $this -> errors[] = 'Password needs at least one letter';
            }

            if ((preg_match('/.*\d+.*/i', $this -> password) == 0)) {
                $this -> errors[] = 'Password needs at least one number';
            }
        }
      }

      /**
       * See if a user record already exists with specified email
       * 
       * @param string $email Email address to search for
       * @param integer $ignoreId Return false anyway if the record found has this ID
       * 
       * @return boolean True if a record already exists with specified email, false otherwise
       * 
       */
      public static function emailExists($email, $ignoreId = null) {

        $user = static::findByEmail($email);

        if ($user) {
          if ($user -> id != $ignoreId) {
            return true;
          }
        }
        
        return false;
      }

        /**
       * Find a user model by email address
       * 
       * @param string $email Email address to search for
       * 
       * @return mixed User object if found, false otherwise
       * 
       */
      public static function findByEmail($email) {

        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':email', $email, PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetch();

      }

        /**
       * Authenticate a user by email and password
       * 
       * @param string $email Email address
       * @param string $password password
       * 
       * @return mixed User object if found, false if authentication fails
       * 
       */
      public static function authenticate($email, $password) {

        $user = static::findByEmail($email);

        if ($user && $user -> isActive) {
            if (password_verify($password, $user -> passwordHash)) {
                return $user;
            }
        }

        return false;
      } 

        /**
       * Find a user model by id
       * 
       * @param integer $id The user id
       * 
       * @return mixed User object if found, false otherwise
       * 
       */
      public static function findById($id) {

        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':id', $id, PDO::PARAM_INT);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();

        return $stmt -> fetch();

      }

      /**
       * Remember login by inserting a new unique token into the remembered_logins table 
       * for this user record
       * 
       * @return boolean True if the login was remembered successfully, false otherwise
       * 
       */
      public function rememberLogin() {

        $token = new Token();
        $hashedToken = $token -> getHashedTokenValue();
        $this -> tokenValue = $token -> getTokenValue();

        $this -> expireTokenTimestamp = time() + 60 * 60 * 24 * 30; // 30 days from now

        $sql = 'INSERT INTO remembered_logins (tokenHash, userId, expiresAt)
              VALUES (:hashedToken, :userId, :expireTokenTimestamp)'; 

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':hashedToken', $hashedToken, PDO::PARAM_STR);
        $stmt -> bindValue(':userId', $this -> id, PDO::PARAM_INT);
        $stmt -> bindValue(':expireTokenTimestamp', date('Y-m-d H:i:s', $this -> expireTokenTimestamp), PDO::PARAM_STR);

        return $stmt -> execute();

      }

      /**
       * Send password reset instructions to the user specified
       * 
       * @param string $email The email address
       * 
       * @return void
       * 
       */
      public static function sendPasswordReset($email) {

        $user = static::findByEmail($email);

        if ($user) {
            if ($user -> startPasswordReset()) {
              
                $user -> sendPasswordResetEmail();
              
            }
        }
      }

      /**
       * Start the password reset process by generating a new token and expiry
       * 
       * @return void
       */
      public function startPasswordReset() {

        $token = new Token();
        $hashedToken = $token -> getHashedTokenValue();
        $this -> passwordResetTokenValue = $token -> getTokenValue();

        $this -> passwordResetExpiresAt = time() + 60 * 60 * 2; // 2 hours from now

        $sql = 'UPDATE users
                SET passwordResetHash = :hashedToken,
                    passwordResetExpiresAt = :passwordResetExpiresAt
                WHERE id = :id'; 

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':hashedToken', $hashedToken, PDO::PARAM_STR);
        $stmt -> bindValue(':passwordResetExpiresAt', date('Y-m-d H:i:s', $this -> passwordResetExpiresAt), PDO::PARAM_STR);
        $stmt -> bindValue(':id', $this -> id, PDO::PARAM_INT);

        return $stmt -> execute();

      }

      /**
       * Send password reset instructions in an email to the user
       * 
       * @return void
       * 
       */
      protected function sendPasswordResetEmail() {

          $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this -> passwordResetTokenValue;

          $text = View::getTemplate('Password/resetEmail.txt', ['url' => $url]);
          $html = View::getTemplate('Password/resetEmail.html', ['url' => $url]);

          Mail::send($this -> email, 'Password reset', $text, $html);

      }

      /**
       * Find a user model by password reset token and expiry
       * 
       * @param string $token Password reset token sent to user
       * 
       * @return mixed User object if found and the token has not expired, null otherwise
       * 
       */
      public static function findByPasswordReset($token) {

        $token = new Token($token);
        $hashedToken = $token -> getHashedTokenValue();

        $sql = 'SELECT * FROM users
                WHERE passwordResetHash = :hashedToken';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':hashedToken', $hashedToken, PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt -> execute();

        $user = $stmt -> fetch();

        if ($user) {

          // check if token has not expired
          if (strtotime($user -> passwordResetExpiresAt) > time()) {
            return $user;
          }
        }
      }

      /**
       * Reset the password
       * 
       * @param string $password The new password
       * 
       * @return boolean True if the password was updated successfully, false otherwise
       * 
       */
      public function resetPassword($password) {

        $this -> password = $password;

        $this -> validate();

        if (empty($this -> errors)) {
          $password_hash = password_hash($this -> password, PASSWORD_DEFAULT);

          $sql = 'UPDATE users 
                  SET passwordHash = :passwordHash,
                      passwordResetHash = NULL,
                      passwordResetExpiresAt = NULL
                  WHERE id = :id';

          $db = static::getDB();
          $stmt = $db -> prepare($sql);
          $stmt -> bindValue(':passwordHash', $password_hash, PDO::PARAM_STR);
          $stmt -> bindValue(':id', $this -> id, PDO::PARAM_INT);

          return $stmt -> execute();
        }
        
        return false;

      }

      /**
       * Send an email to the user containing the activation link
       * 
       * @return void
       * 
       */
      public function sendActivationEmail() {

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this -> activationTokenValue;

        $text = View::getTemplate('Signup/activationEmail.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activationEmail.html', ['url' => $url]);

        Mail::send($this -> email, 'Account activation', $text, $html);

    }

    /**
     * Activate the user account with the specified activation token
     * 
     * @param string $value Activation token from URL
     * 
     * @return void
     * 
     */
    public static function activate($token) {

      $token = new Token($token);
      $hashedToken = $token -> getHashedTokenValue();

      $sql = 'UPDATE users
                SET isActive = 1,
                    activationHash = NULL
                WHERE activationHash = :hashedToken'; 

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':hashedToken', $hashedToken, PDO::PARAM_STR);

        $stmt -> execute();

    }

    /**
     * Update the user's profile
     * 
     * @param array $data Data from the edit profile form
     * 
     * @return boolean True if the data was updated, false otherwise
     * 
     */
    public function updateProfile($data) {
      
      $this -> name = $data['name'];
      $this -> email = $data['email'];

      // Only validate and update the password if a value provided
      if ($data['password'] != '') {
        $this -> password = $data['password'];
      }

      $this -> validate();

      if (empty($this -> errors)) {
        $sql = 'UPDATE users
                SET name = :name,
                    email = :email';

        // Add password if it is set
        if (isset($this -> password)) {
          $sql .= ', passwordHash = :passwordHash';
        }

        $sql .= "\nWHERE id = :id";    

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':name', $this -> name, PDO::PARAM_STR);
        $stmt -> bindValue(':email', $this -> email, PDO::PARAM_STR);
        $stmt -> bindValue(':id', $this -> id, PDO::PARAM_INT);

        if (isset($this -> password)) {
          $passwordHash = password_hash($this -> password, PASSWORD_DEFAULT);
          $stmt -> bindValue(':passwordHash', $passwordHash, PDO::PARAM_STR);
        }
       
        return $stmt -> execute();        
      }

      return false;
    }

     /**
     * Get last inserted id from database
     * 
     * @return integer Last inserted id
     * 
     */
    static function getLastInsertedId() {

        $db = static::getDB();
        return $db -> lastInsertId();

    }

    /**
     * Set default income, expense and payment categories to user
     * 
     * @return void
     * 
     */
    public static function setDefaultCategoriesToUser() {

      $userId = static::getLastInsertedId();

      static::setDefaultIncomeCategories($userId);
      static::setDefaultExpenseCategories($userId);
      static::setDefaultPaymentMethods($userId);

    }


    /**
     * Set default income categories to user
     * 
     * @return void
     * 
     */
    static function setDefaultIncomeCategories($userId) {

      $sql = 'INSERT INTO incomes_category_assigned_to_users (incomes_category_assigned_to_users.userId, 
                                                              incomes_category_assigned_to_users.name) 
              SELECT :userId, incomes_category_default.name 
              FROM incomes_category_default'; 

      $db = static::getDB();
      $stmt = $db -> prepare($sql);
      $stmt -> bindValue(':userId', $userId, PDO::PARAM_INT);

      $stmt -> execute();

    }

    /**
     * Set default expense categories to user
     * 
     * @return void
     * 
     */
    static function setDefaultExpenseCategories($userId) {

      $sql = 'INSERT INTO expenses_category_assigned_to_users (expenses_category_assigned_to_users.userId, 
                                                              expenses_category_assigned_to_users.name) 
              SELECT :userId, expenses_category_default.name 
              FROM expenses_category_default'; 

      $db = static::getDB();
      $stmt = $db -> prepare($sql);
      $stmt -> bindValue(':userId', $userId, PDO::PARAM_INT);

      $stmt -> execute();

    }

    /**
     * Set default payment methods to user
     * 
     * @return void
     * 
     */
    static function setDefaultPaymentMethods($userId) {

      $sql = 'INSERT INTO payment_methods_assigned_to_users (payment_methods_assigned_to_users.userId, 
                                                            payment_methods_assigned_to_users.name) 
              SELECT :userId, payment_methods_default.name 
              FROM payment_methods_default'; 

      $db = static::getDB();
      $stmt = $db -> prepare($sql);
      $stmt -> bindValue(':userId', $userId, PDO::PARAM_INT);

      $stmt -> execute();

    }

    /**
     * Delete the user's profile
     * 
     * @return boolean True if the user account was deleted, false otherwise
     * 
     */
    public function deleteUser() {

        $sql = 'DELETE FROM users
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':id', $this -> id, PDO::PARAM_INT);
       
        return $stmt -> execute();        
    }

 }