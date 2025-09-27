<?php
/**
 * AdminLogin.php
 *
 * @package sessions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace sessions;

/**
 * Checks the required login rights and last active time for a User that has an Administrator role
 *
 * @link ../graphs/sessions%20Class%20Diagram.svg Sessions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class AdminLogin extends LoginRequired {
    
    /**
     * Returns true when User has Administrator rights
     * 
     * @return boolean
     */
    #[\Override]
    protected function checkMemberRights(): bool {
        if(! $this->user->active){
            return false;
        }
        
        if($this->user->role !== "A") {
            return false;
        }
        
        return true;
    }

    /**
     * Set the numbers of minutes for the session of an Adminstrator 
     */
    #[\Override]
    protected function initLastActive(): void {
        $this->setLastActive(15);
    }

}
