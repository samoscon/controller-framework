<?php
/**
 * NoLoginRequired.php
 *
 * @package controllerframework\sessions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\sessions;

/**
 * Returns always true for those Commands where no login is required (e.g. public web pages)
 *
 * @link ../graphs/sessions%20Class%20Diagram.svg Sessions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class NoLoginRequired extends Login {
    
    /**
     * No login validation is required
     * 
     * @return boolean Always true on this level
     */
    public function validate(): bool {
        return true;
    }

}