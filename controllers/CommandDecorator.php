<?php
/**
 * CommandDecorator.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Implementation of design pattern 'Decorator'
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class CommandDecorator extends Command {
    /**
     *
     * @var Command Handle to parent Command (another CommandDecorator or a specialized Command)
     */
    protected ?Command $command = null;

    /**
     * Sets the parent Command $command
     * 
     * @param \controllers\Command $command
     */
    protected function setCommand(Command $command): void {
        $this->command = $command;
    }
    
    /**
     * Template method for the decorator
     * 
     * @param \registry\Request $request
     * @return int Returns 1 of the status constants in Command
     */
    #[\Override]
    final public function doExecute(\registry\Request $request): int {
        $this->initCommand();
        $status = $this->doExecuteDecorator($request);
        if ($status) {
            return $status;
        }
        return $this->command->execute($request);
    }
   
    /**
     * Abstract function to be specialized in a subclass. With this method the 
     * subclass will set the Command it wants to decorate
     */
    abstract public function initCommand(): void;
    
    /**
     * Abstract function to be specialized in a subclass
     * 
     * @param \resistry\Request $request
     */
    abstract public function doExecuteDecorator(\registry\Request $request): void;
}
