<?php
/**
 * Login.php
 *
 * @package sessions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace sessions;

/**
 * Superclass to validate whether the User has a valid login into a session
 * Implements design pattern 'Strategy'
 *
 * @link ../graphs/sessions%20Class%20Diagram.svg Sessions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Login {

    /**
     * Validates the validity of a login for the requested Command
     * 
     * @return boolean Result of the validation of login
     */
    abstract public function validate(): bool;

}