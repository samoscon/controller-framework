<?php
/**
 * HttpRequest.php
 *
 * @package registry
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace registry;

/**
 * Request coming from an internet browser
 *
 * @link ../graphs/registry%20Class%20Diagram.svg Registry class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class HttpRequest extends Request {
    
    /**
     * Initializes the request if it comes from an internet browser
     */
    #[\Override]
    public function init(): void {
        $this->properties = $_REQUEST;
        $this->path = (empty($_SERVER['PATH_INFO'])) ? "/" : $_SERVER['PATH_INFO'];
    }
    
    /**
     * Forwards the request to the next path
     * 
     * @param string $path Name of a path as defined in controls.xml
     */
    #[\Override]
    public function forward(string $path): void {
        if(isset($_COOKIE['originalPath'])) {
            $reg = \registry\Registry::instance();
            $conf = $reg->getAppConfig();
            $forwardpath = $conf->get("forwardpath");
            $path = $forwardpath.$_COOKIE['originalPath'];
            setcookie('originalPath', '', 0, '/');
        }
        header("Location: $path");
        exit();
    }
}
