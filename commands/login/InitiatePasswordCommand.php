<?php
/**
 * Specialization of a Command
 *
 * @package commands\login
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\login;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class InitiatePasswordCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        /** variables */
        $usernameIsFound = true;
        $userIsEmpty = false;
        
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = filter_var($request->get('username'), FILTER_SANITIZE_EMAIL);
            $userIsEmpty = $username ? false : true;

            $memberid = $this->reg->getLoginManager()->validateUsername($username);
            $usernameIsFound = $memberid ? true : false;
            
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
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());
    }

}