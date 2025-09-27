<?php
/**
 * CliRequest.php
 *
 * @package registry
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace registry;

/**
 * Request coming from a command line
 *
 * @link ../graphs/registry%20Class%20Diagram.svg Registry class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class CliRequest extends Request {
    
    /**
     * Initializes the Request if it comes from a command line
     */
    #[\Override]
    public function init(): void {
        $args = $_SERVER['argv'];
        
        foreach ($args as $arg) {
            if (preg_match("/^path:(\S+)/", $arg, $matches)) {
                $this->path = $matches[1];
            } else {
                if (strpos($arg, '=')) {
                    list($key, $val) = explode("=", $arg);
                    $this->setProperty($key, $val);
                }
            }
        }
        
        $this->path = (empty($this->path)) ? "/" : $this->path;
    }
    
    /**
     * Forwards the request to the next path
     * 
     * @param string $path Name of a path as defined in controls.xml
     */
    #[\Override]
    public function forward(string $path): void {
        $_SERVER['argv'][] = "path:$path";
        Registry::reset();
        \controllers\Controller::run();
    }
}
