<?php
/**
 * RenderComponentDescriptor.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Contains the info related to a path as described in the controls.xml file in object format
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class RenderComponentDescriptor {
    
    /**
     *
     * @var string The path as defined in controls.xml
     */
    private string $path;
    
    /**
     *
     * @var string The related class as defined in controls.xml
     */
    private string $cmdstr;
    
    /**
     *
     * @var array Array of related renderers (depending on status) 
     */
    private array $renderers = [];
    
    /**
     *
     * @var Command Static handle referring to Command as class
     */
    private static $refcmd;

    /**
     * Constructor
     * 
     * @param string $path
     * @param string $cmdstr
     */
    public function __construct(string $path, string $cmdstr) {
        self::$refcmd = new \ReflectionClass(Command::class);
        $this->path = $path;
        $this->cmdstr = $cmdstr;
    }

    /**
     * Returns the Command subclass related to this path
     * 
     * @return Command
     */
    public function getCommand(): Command {
        return $this->resolveCommand($this->cmdstr);
    }

    /**
     * Sets a RenderComponent for the specified status as defined in the controls.xml file.
     * RenderComonent can be a render of a view, a render to forward to a new path or another 
     * type of render (ajax response, file to download, ics (agenda), file, etc.)
     * 
     * @param int $status
     * @param RenderComponent $view
     */
    public function setRenderer(int $status, RenderComponent $renderer): void {
        $this->renderers[$status] = $renderer;
    }

    /**
     * Returns a RenderComponent.
     * RenderComonent can be a render of a view, a render to forward to a new path or another 
     * type of render (ajax response, file to download, ics (agenda), file, etc.)
     * View can be a forwardView or another type of response (ajax response, file to download, ics (agenda) file, etc.)
     * 
     * @param \registry\Request $request
     * @return RenderComponent
     * @throws \Exception No renderer found
     */
    public function getRenderer(\registry\Request $request): RenderComponent {
        $status = $request->getCmdStatus();
        $status = (is_null($status)) ? 0 : $status;
        
        if (isset($this->renderers[$status])) {
            return $this->renderers[$status];
        }
        
        
        if (isset($this->renderers[0])) {
            return $this->renderers[0];
        }
        
        throw new \Exception("no renderer found");
    }

    /**
     * Helper function to find the concrete subclass of Command on basis of a class name
     * 
     * @param string $class
     * @return Command
     * @throws \Exception Command class not found
     */
    private function resolveCommand(string $class): Command {
        if (is_null($class)) {
            throw new \Exception("unknown class");
        }
        
        if (!class_exists($class)) {
            throw new \Exception("class $class not found");
        }
        
        $refclass = new \ReflectionClass($class);
        
        if(! $refclass->isSubclassOf(self::$refcmd)) {
            throw new \Exception("command $class is not a Command");
        }
        
        return $refclass->newInstance();
    }
}
