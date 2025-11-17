<?php
/**
 * HandleRequestController.php
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
 * MVC implementation of the HandleRequestController
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class HandleRequestController {
    
    /**
     *
     * @var string Classname of DefaultCommand 
     */
    private static $defaultcmd = DefaultCommand::class;
    
    /**
     *
     * @var string Name of the default view 
     */
    private static string $defaultview = "/errorView";
    
    /**
     * Returns the command related to the path in the request.
     * Definition of commands per path can be found in the controls.xml under the property "class="
     * 
     * E.g. <command path="/home" class="\controllers\commands\user\HomeCommand">
     * 
     * @param Request $request
     * @return Command
     */
    protected function getCommand(Request $request): Command {
        try {
            $cmd = $this->getDescriptor($request)->getCommand();                
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return new self::$defaultcmd();
        }
        
        return $cmd;
    }
    
    /**
     * Returns the render component related to the path in the request and depending 
     * on the status of the executed command
     * 
     * For views can be found in the controls.xml under the property <view name=""> or otherwise a different type is indicated (download, ajax, etc.)
     * 
     * @param Request $request
     * @return RenderComponent
     */
    protected function getRenderer(Request $request): RenderComponent {
        try {
            $renderer = $this->getDescriptor($request)->getRenderer($request);
        } catch (\Exception $exc) {
            return new ViewRenderComponent(self::$defaultview);
        }
        
        return $renderer;
    }
    
    /**
     * Helper class that returns a RenderComponentDescriptor that provides a full description for a given path name
     * linking that path to the correct Command class and the correct RenderComponents 
     * (eventually depending on status of the executed command or on the type of datarequest)
     * 
     * @param Request $request
     * @return RenderComponentDescriptor
     * @throws \Exception
     */
    private function getDescriptor(Request $request): RenderComponentDescriptor {
        $reg = Registry::instance();
        $commands = $reg->getCommands();
        $path = $request->getPath();
        $descriptor = $commands->get($path);
        
        if (is_null($descriptor) || !$descriptor) {
            throw new \Exception("path $path bestaat niet. Kijk de url na.");
        }

        return $descriptor;
    }
    
    /**
     * Implementation of the handling of a request. To be implemented in the respective frameworks via their subclass
     * 
     * @param Request $request
     */
    abstract public function handleRequest(Request $request): void;
}