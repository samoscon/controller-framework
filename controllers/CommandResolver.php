<?php
/**
 * CommandResolver.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * CommandResolver is a simplified version of the Application Controller framework.
 * It works without the 'Data Injection' of controls.xml
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class CommandResolver {
    
    /**
     *
     * @var DefaultCommand Handle to the DefaultCommand class 
     */
    private static $defaultcmd = DefaultCommand::class;
    
    /**
     *
     * @var Command Reference on class level to Command 
     */
    private static $refcmd = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        self::$refcmd = new \ReflectionClass(Command::class);
    }
    
    /**
     * Returns a concrete subclass of Command related to the path depending of the configuration in the ini file.
     * 
     * @param \registry\Request $request
     * @return Command
     */
    public function getCommand(\registry\Request $request): Command {
        $reg = \registry\Registry::instance();
        $path = $request->get(search);
        $class = $reg->getCommands()->get($path);
        
        if (is_null($class)) {
            $request->addFeedback("path $path not matched");
            return new self::$defaultcmd;
        }
         
        if (!class_exists($class)) {
            $request->addFeedback("class $class not found");
            return new self::$defaultcmd;
        }
         
        $refclass = new \ReflectionClass($class);
        
        if(! $refclass->isSubclassOf(self::$refcmd)) {
            $request->addFeedback("command '$refclass' is not a Command");
            return new self::$defaultcmd;            
        }
        
        $request->addFeedback($refclass->name);
        return $refclass->newInstance();
    }
}