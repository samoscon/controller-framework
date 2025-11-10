<?php
/**
 * DefaultCommand.php
 *
 * @package commands
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands;

/**
 * Subclass of Command. Must be at minimum present in the MVC framework. 
 * Will lead the visitor of your website to the first page (home screen, welcome screen, login screen, etc.)
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class DefaultCommand extends \controllerframework\controllers\Command {
    
    /**
     * Concrete specialization of execute method in Command
     * 
     * @param \registry\Request $request
     * @return int Returns status of the executed command
     */
    #[\Override]
    public function doExecute(\controllerframework\registry\Request $request): int {
        /** variables */
        $passwordIsValid = $userNameIsFound = true;
        $passwordIsEmpty = $userIsEmpty = false;

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {    
            //** request variables and validate them */
            $user = filter_var($request->get("user"), FILTER_VALIDATE_EMAIL);
            $userIsEmpty = $user ? false : true;

            $password = filter_var($request->get("password"), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $passwordIsEmpty = $password ? false : true;

            $memberid = $this->reg->getLoginManager()->validateUsername($user);
            if(! $memberid) {
                $userNameIsFound = false; //Member is not found or is no longer active
            } else {
                $passwordIsValid = $this->reg->getLoginManager()->validatePassword($memberid, $password);
            }
            
            $rememberMe = filter_var($request->get("rememberMe"), FILTER_DEFAULT) ? true : false;
            
            //** perform your post request via the controller */
            if (!$userIsEmpty && $userNameIsFound && !$passwordIsEmpty && $passwordIsValid) {
                $user = $this->reg->getLoginManager()->login($memberid, $rememberMe);
                return $this->loginChecks($user);
            } 
        }
        
        $this->addResponses($request, ["passwordIsValid" => $passwordIsValid, "userNameIsFound" => $userNameIsFound, 
            "passwordIsEmpty" => $passwordIsEmpty, "userIsEmpty" => $userIsEmpty]);
        
//        include (realpath(__DIR__ . "/../..") . "/Views/defaultView.php");
        return self::CMD_DEFAULT;
    }
    
    /**
     * Concrete specialization of setLoginLevel method in Command.
     * Needs to set 1 of the concrete subclasses of \sessions\Login
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }
}
