<?php
/**
 * DefaultCommand.php
 *
 * @package controllerframework\commands
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\controllers;

use controllerframework\sessions\NoLoginRequired;
use controllerframework\registry\Request;

/**
 * This DefaultCommand will be instantiated if no correct command for your path was found.
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class DefaultCommand extends Command {
    #[\Override]
    public function doExecute(Request $request): int {
        $request->addFeedback("No command defined for your path. Check your controls.xml file");
        return self::CMD_ERROR;
    }
    
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new NoLoginRequired());        
    }
}
