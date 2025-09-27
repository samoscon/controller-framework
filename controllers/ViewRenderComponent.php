<?php
/**
 * ViewRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Renders a view towards the browser
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ViewRenderComponent implements RenderComponent {
    /**
     *
     * @var string Name of the view (without extension .php) 
     */
    private string $name = '';
    
    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    /**
     * Renders a view towards the browser
     * 
     * @param \registry\Request $request
     * @throws \Exception Template of the view has not been found
     */
    #[\Override]
    public function render(\registry\Request $request): void {
        $reg = \registry\Registry::instance();
        $conf = $reg->getAppConfig();
        $path = $conf->get("templatepath");
        
        if (is_null($path)) {
            throw new \Exception("no template directory");
        }
        
        $fullpath = $path . $this->name.".php";
        
        if(!file_exists($fullpath)) {
            throw new \Exception("no template at $fullpath");
        }
        
        include ($fullpath);
    }
}
