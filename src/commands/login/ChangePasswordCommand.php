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
class ChangePasswordCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int Returns a status e.g. CMD_DEFAULT, CMD_OK, CMD_ERROR, etc.
     */
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** variables */
        $passwordIsValid = true;
        $passwordIsEmpty = $password2IsEmpty = false;
        $user = \controllerframework\sessions\User::getInstance();

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password = filter_var($request->get('password'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $passwordIsEmpty = $password ? false : true;
            
            $password2 = filter_var($request->get('password2'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $password2IsEmpty = $password2 ? false : true;

            $passwordIsValid = $password !== $password2 ? false : true;

            if (!$passwordIsEmpty && !$password2IsEmpty && $passwordIsValid) {
                $this->reg->getLoginManager()->changePassword($user, $password);
                $user->ownpwd = 1;
                return $this->loginChecks($user);
            }
        }
        
        /** the page was requested via the GET method or the POST method did not return a status. */
        foreach (get_object_vars($user) as $key => $value) {
            $responses[$key] = $value;
        }
        $responses['passwordIsValid'] = $passwordIsValid;
        $responses['passwordIsEmpty'] = $passwordIsEmpty;
        $responses['password2IsEmpty'] = $password2IsEmpty;
        $responses['returnpath'] = 'logout';
        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }
    
    /**
     * Specialization of getLevelOfLoginRequired. Sets the level of login (User, Admin, No login required, etc.) that is required for this command
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\UserLogin());
    }

}
