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
class ConfirmPasswordCommand extends \controllerframework\controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int Returns a status e.g. CMD_DEFAULT, CMD_OK, CMD_ERROR, etc.
     */
    public function doExecute(\controllerframework\registry\Request $request): int {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            return self::CMD_OK;
        }
        
        return self::CMD_DEFAULT;        
    }

    /**
     * Specialization of getLevelOfLoginRequired. Sets the level of login (User, Admin, No login required, etc.) that is required for this command
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\NoLoginRequired());
    }

}