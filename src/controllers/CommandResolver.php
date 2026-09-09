<?php
/**
 * CommandResolver.php
 *
 * @package controllerframework\controllers
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\controllers;

use controllerframework\registry\Request;
use controllerframework\registry\Registry;

/**
 * CommandResolver is a simplified version of the Application Controller framework.
 * It works without the 'Data Injection' of controls.xml
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class CommandResolver {
    
    /**
     *
     * @var string Classname of DefaultCommand
     */
    private static string $defaultcmd = DefaultCommand::class;
    
    /**
     *
     * @var Command Reference on class level to Command 
     */
    private static ?\ReflectionClass $refcmd = null;    
    
    /**
     * Constructor
     */
    public function __construct() {
        self::$refcmd = new \ReflectionClass(Command::class);
    }
    
    /**
     * Returns a concrete subclass of Command related to the path depending of the configuration in the ini file.
     * 
     * @param Request $request
     * @return Command
     */
    public function getCommand(Request $request): Command {
        $reg = Registry::instance();
        $path = $request->get('search');
        $class = $reg->getCommands()->get($path);

        if ($class === null) {
            $request->addFeedback("path $path not matched");
            return new self::$defaultcmd;
        }

        if (!class_exists($class)) {
            throw new \RuntimeException(
                "Command class '$class' not found for path '$path'."
            );
        }

        $refclass = new \ReflectionClass($class);

        if (!$refclass->isSubclassOf(self::$refcmd)) {
            throw new \RuntimeException(
                "Command class '$class' is not a subclass of " . Command::class . "."
            );
        }

        $request->addFeedback($refclass->name);

        return $refclass->newInstance();
    }

}