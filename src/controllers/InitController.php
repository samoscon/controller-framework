<?php
/**
 * InitController.php
 *
 * @package controllerframework\controllers
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\controllers;

use controllerframework\registry\Request;
use controllerframework\registry\Registry;
use controllerframework\registry\HttpRequest;
use controllerframework\registry\CliRequest;

/**
 * Helper class to initialize the configure options and initialize paths with their ComponentDescriptors
 * 
 * Implements the design pattern 'Data Injection'
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class InitController {
    
    /**
     *
     * @var string Contains the ini file for all application specific options. 
     * 
     * Make sure this file is protected in your .htaccess config 
     */
    private string $config = '';
    
    /**
     *
     * @var string Contains in XML format the description of the paths. 
     */
    private string $controlsfile = '';
    
    /**
     *
     * @var Registry  Handle to Registry
     */
    private Registry $reg;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->reg = Registry::instance();
        $this->config = realpath("./") . "/config/app_options.ini";
    }
    
    /**
     * Set up of the options as defined in $config file and 
     * instantiation of the original Request as received by the web server
     */
    public function init(): void {
        $this->setupOptions();
        
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $request = new HttpRequest();
        } else {
            $request = new CliRequest();
        }
        
        $this->reg->setRequest($request);
    }
    
    /**
     * Set up of the options as defined in $config file 
     * 
     * @throws \Exception When options file could not be found
     */
    private function setupOptions(): void {
        if (! file_exists($this->config)) {
            throw new \Exception("Could not find options file : $this->config");
        }
        
        $options = parse_ini_file($this->config, TRUE);
        
        $conf = new Conf($options['config']);
        $this->reg->setAppConfig($conf);
        
        foreach ($options['globals'] as $name => $global) {
            define($name, $global);
        }
        
        $this->controlsfile = realpath("./") . _CONTROLSFILE;
        $commands = $this->setupCommands($options, $this->controlsfile);
        
        $this->reg->setCommands($commands);
    }
    
    /**
     * Set up of the commands as defined in $config file or in the controls.xml file
     * 
     * @param array $options from the config file
     * @param string Contains the name of controls file 
     * @return Conf Map of Commands
     */
    abstract protected function setupCommands(array $options, string $controlsfile): Conf;
}
