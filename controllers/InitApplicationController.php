<?php
/**
 * InitApplicationController.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Subclass of InitController. Implementing the setting of Commands according to the Application Controller framework
 *
 * @author dirk
 */
class InitApplicationController extends InitController {
    /**
     * Set up of the commands as defined in $config file or in the controls.xml file
     * 
     * @var array $options from the config file
     * @var string Contains the name of controls file 
     * @return Conf Map of Commands
     */
    #[\Override]
    protected function setupCommands(array $options, string $controlsfile): Conf {
        $vcc = new RenderCompiler();
        $commands = $vcc->parseFile($controlsfile);
        
        return $commands;
    }
}
