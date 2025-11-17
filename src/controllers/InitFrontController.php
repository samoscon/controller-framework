<?php
/**
 * InitFrontController.php
 *
 * @package controllerframework\controllers
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\controllers;

/**
 * Subclass of InitController. Implementing the setting of Commands according to the Front Controller framework
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class InitFrontController extends InitController {

    /**
     * Set up of the commands as defined in $config file or in the controls.xml file
     * 
     * @param array $options from the config file
     * @param string $controlsfile Contains the name of controls file 
     * @return Conf Map of Commands
     */
    #[\Override]
    protected function setupCommands(array $options, string $controlsfile): Conf {
        $commands = new Conf($options['commands']);
        
        return $commands;
    }
}
