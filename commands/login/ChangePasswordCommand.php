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
class ChangePasswordCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    public function doExecute(\registry\Request $request): int {
        /** variables */
        $passwordIsValid = true;
        $passwordIsEmpty = $password2IsEmpty = false;
        $user = \sessions\User::getInstance();

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
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\UserLogin());
    }

}