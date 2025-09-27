<?php
/**
 * NoLoginRequired.php
 *
 * @package sessions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace sessions;

/**
 * Returns always true for those Commands where no login is required (e.g. public web pages)
 *
 * @link ../graphs/sessions%20Class%20Diagram.svg Sessions class diagram
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class NoLoginRequired extends Login {
    
    /**
     * No login validation is required
     * 
     * @return boolean Always true
     */
    public function validate(): bool {
        return true;
    }

}