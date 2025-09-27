<?php
/**
 * User.php
 *
 * @package sessions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace sessions;

/**
 * Refers to a Member (via the $memberID) that is owning the SESSION
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class User {
    /**
     *
     * @var Member Implements design pattern 'Singleton'
     */
    private static ?\model\members\Member $instance = null;
    
    
    /**
     * Method to initialise a new User object
     * 
     * @param int $memberid id of a member in the database
     * @return \members\Member or null if no memberID is available in the SESSION
     */
    public static function getInstance(int $memberid = 0): ?\model\members\Member {
        if (is_null(self::$instance)) {
            try {
                self::$instance = \model\Member::find($memberid);            
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        
        return self::$instance;
    }
}