<?php
/**
 * DefaultCommand.php
 *
 * @package commands
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\controllers;

use controllerframework\sessions\NoLoginRequired;
use controllerframework\registry\Request;

/**
 * Description of DefaultCommand
 *
 * @author dirk
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
