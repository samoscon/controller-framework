<?php
/**
 * Specialization of a Command
 *
 * @package controllerframework\commands\login
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\commands\login;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class InitiatePasswordCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int Returns a status e.g. CMD_DEFAULT, CMD_OK, CMD_ERROR, etc.
     */
    
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** variables */
        $usernameIsFound = true;
        $userIsEmpty = false;
        
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = filter_var($request->get('username'), FILTER_SANITIZE_EMAIL);
            $userIsEmpty = $username ? false : true;

            $memberid = $this->reg->getLoginManager()->validateUsername($username);
            $usernameIsFound = $memberid ? true : false;
            
            if($usernameIsFound){            
                $member = \model\Member::find($memberid);
            
                if(_MINLEVELTOLOGIN === 'A' && !$member->isAdministrator()) {
                    $usernameIsFound = false;
                }
            }
            
           if (!$userIsEmpty && $usernameIsFound) {
                $this->reg->getLoginManager()->initiatePassword($memberid);
                return self::CMD_OK;
            }
        }

        /** the page was requested via the GET method or the POST method did not return a status. */
        $this->addResponses($request, [
            'usernameIsFound' => $usernameIsFound,
            'userIsEmpty' => $userIsEmpty,
            'returnpath' => _APPDIR.'']);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired. Sets the level of login (User, Admin, No login required, etc.) that is required for this command
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }

}
