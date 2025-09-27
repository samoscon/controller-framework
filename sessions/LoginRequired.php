<?php
/**
 * LoginRequired.php
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
abstract class LoginRequired extends Login {
    
    /**
     *
     * @var int Max nbr of allowed minutes since last active session
     */
    private int $lastActive = 900;
    
    /**
     *
     * @var LoginManager Handle to the LoginManager facade 
     */
    private ?LoginManager $loginmanager = null;
    
    /**
     *
     * @var User Handle to the current User of the session 
     */
    protected ?\model\members\Member $user = null; 
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->loginmanager = \registry\Registry::instance()->getLoginManager();
    }

    /**
     * Validates validity of the login of the User for a certain Command
     * Implements design pattern 'Template Method'
     * 
     * @return boolean
     */
    #[\Override]
    public function validate(): bool {
        $this->initLastActive();
        $this->user = $this->loginmanager->getUser();
        if(!$this->user || !$this->checkLastActiveTime() || !$this->checkMemberRights()) {
            $this->loginmanager->logout();
            return false;
        }
        return true;
    }
    
    /**
     * Checks whether the last request within this session has been executed 
     * within the time limit as defined in $lastactive
     * 
     * @return boolean
     */
    private function checkLastActiveTime(): bool {
        $timeOfInactivityAllowed = $_SESSION['rememberMe'] ? ($this->lastActive * 1000) : $this->lastActive;

        if(($_SESSION['lastActive'] < time() - 1 * $timeOfInactivityAllowed)) {
            return false;
        }
        $_SESSION['lastActive'] = time();
        setcookie('PHPSESSID', session_id(), time() + (1000 * $this->lastActive));
        return true;
    }
    
    /**
     * Sets the $lastactive property of the concrete object
     * 
     * @param int $numberOfMinutes (e.g. UserLoginRequired => 60; AdminLoginRequired => 15)
     */
    protected function setLastActive(int $numberOfMinutes): void {
        $this->lastActive = $numberOfMinutes * 60;
    }

    /**
     * To be implemented in concrete subclass to setLastActive
     */
    abstract protected function initLastActive(): void;
    
    /**
     * To be implemented in concrete subsclass to check the specific member rights 
     * depending on the User type
     */
    abstract protected function checkMemberRights(): bool;

}
