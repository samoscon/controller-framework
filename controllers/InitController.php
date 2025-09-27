<?php
/**
 * InitController.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

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
     * @var \registry\Registry  Handle to Registry
     */
    private \registry\Registry $reg;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->reg = \registry\Registry::instance();
        $this->config = realpath("./") . "/config/app_options.ini";
        $this->controlsfile = realpath("./") . "/config/controls.xml";
    }
    
    /**
     * Set up of the options as defined in $config file and 
     * instantiation of the original Request as received by the web server
     */
    public function init(): void {
        $this->setupOptions();
        
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $request = new \registry\HttpRequest();
        } else {
            $request = new \registry\CliRequest();
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
        
        $commands = $this->setupCommands($options, $this->controlsfile);
        
        $this->reg->setCommands($commands);
    }
    
    /**
     * Set up of the commands as defined in $config file or in the controls.xml file
     * 
     * @var array $options from the config file
     * @var string Contains the name of controls file 
     * @return Conf Map of Commands
     */
    abstract protected function setupCommands(array $options, string $controlsfile): Conf;
}