<?php
/**
 * ForwardRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Renders a new path as forward location to the browser
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ForwardRenderComponent implements RenderComponent {
    
    /**
     *
     * @var string New path name (where to forward to)
     */
    private string $path = '';
    
    /**
     * Constructor
     * 
     * @param string $path New path name
     */
    public function __construct(string $path) {
        $this->path = $path;
    }
    
    /**
     * Renders a ViewComponent as forward location to the browser
     * 
     * @param \registry\Request $request If the new path needs query parameters, 
     * the request needs 'forwardqueryparameters' set as a named array.
     * @throws \Exception If no forward path is set in the ini file
     */
    public function render(\registry\Request $request): void {
        $reg = \registry\Registry::instance();
        $conf = $reg->getAppConfig();
        $path = $conf->get("forwardpath");
        
        if (is_null($path)) {
            throw new \Exception("no forward hostname - path");
        }
        
        $fullpath = $path.  $this->path;
        
        $requestparams = $request->get("forwardqueryparams");
        $params = '';
        if($requestparams){
            $params = '?';
            foreach ($requestparams as $key => $value) {
                $params .= $key . '=' . $value . '&';
            }
        }
        $request->forward($fullpath . $params);
    }
}
