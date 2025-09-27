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
class ConfirmPasswordCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    public function doExecute(\registry\Request $request): int {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            return self::CMD_OK;
        }
        
        $request->addFeedback('Een mail is naar het opgegeven mailadres verstuurd !! Log opnieuw in met het opgegeven wachtwoord.');
        return self::CMD_DEFAULT;        
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());
    }

}