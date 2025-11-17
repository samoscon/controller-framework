<?php
/**
 * Specialization of a Command
 *
 * @package commands\admin
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class AdminHomeCommand extends \controllerframework\controllers\CommandDecorator {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \controllerframework\registry\Request $request
     * @return int or null
     */
    #[\Override]
    public function doExecuteDecorator(\controllerframework\registry\Request $request): ?int {
        /** Put your code here. Following lines are meant as an example */
        $this->addResponses($request, [
            'title' => _MAILFROMNAME.' Admin',
            'labelActivitiesList' => 'Activiteiten', //Contains the title on top of the activitieslist
            'labelMembersMenu' => 'Leden' //Contains the name of the search for members in the menu
            ]); 
            return null;
    }

    /**
     * Specialization of initCommand
     */
    #[\Override]
    public function initCommand(): void {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $classname = str_replace('commands', 'membersactivities\commands', $classname);
        $this->setCommand(new $classname);        
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \controllerframework\sessions\AdminLogin());
    }
}
