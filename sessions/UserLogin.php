<?php
/**
 * UserLogin.php
 *
 * @package sessions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace sessions;

/**
 * Checks the required login rights and last active time for a User that has a User role
 *
 * @link ../graphs/sessions%20Class%20Diagram.svg Sessions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class UserLogin extends LoginRequired {

    /**
     * Checks the required member rights for a User
     * 
     * @return boolean
     */
    #[\Override]
    protected function checkMemberRights(): bool {
        if(! $this->user->active){
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets $lastActive for a User in minutes
     */
    #[\Override]
    protected function initLastActive(): void {
        $this->setLastActive(60);
    }


}
