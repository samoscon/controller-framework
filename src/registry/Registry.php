<?php
/**
 * Registry.php
 *
 * @package controllerframeworkregistry
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\registry;

use controllerframework\controllers\
{
    InitController,
    HandleRequestController,
    InitApplicationController,
    HandleRequestApplicationController,
//    InitFrontController,
//    HandleRequestFrontController,
    Conf
};
use controllerframework\sessions\LoginManager;

/**
 * Implements design pattern 'Registry' to manage application wide variables
 *
 * @link ../graphs/registry%20Class%20Diagram.svg Registry class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Registry {
    /**
     *
     * @var Registry Implements design pattern 'Singleton'
     */
    private static ?Registry $instance = null;
    
    /**
     *
     * @var Request Sent to the Web Server
     */
    private ?Request $request = null;
    
    /**
     *
     * @var InitController Handle to help the class that will initialize 
     * the Registry with App config info and commands
     */
    private ?InitController $initController = null;
    
    /**
     *
     * @var HandleRequestController Handle to help the class that will initialize 
     * the Registry with App config info and commands
     */
    private ?HandleRequestController $handleRequestController = null;
    
    /**
     *
     * @var Conf Helper class for handling Configurations
     */
    private ?Conf $conf = null;
    
    /**
     *
     * @var Conf of Commands 
     */
    private ?Conf $commands = null;
    
    /**
     *
     * @var \sessions\LoginManager Handle to the LoginManager facade
     */
    private ?LoginManager $loginManager = null;
    
    /**
     *
     * @var PDO PHP Database Object 
     */
    private ?\PDO $db = null;
    
    /**
     * Constructor
     */
    private function __construct() {
        
    }
    
    /**
     * The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
     * thus eliminating the possibility of duplicate objects.
     */
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    /**
     * The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
     * thus eliminating the possibility of duplicate objects.
     */
    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }
    
    /**
     * Implements design pattern 'Singleton'
     * 
     * @return Registry
     */
    public static function instance(): self {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Reset of the registry
     */
    public static function reset() {
        self::$instance = null; 
    }
    
    /**
     * Returns the current request (Http or Client)
     * 
     * @return Request Http or Client
     * @throws \Exception If no request has been set
     */
    public function getRequest(): Request {
        if(is_null($this->request)) {
            throw new \Exception("No request set");
        }
        
        return $this->request;
    }
    
    /**
     * Set $request The request will be set from the InitController
     * 
     * @param Request $request
     */
    public function setRequest(Request $request): void {
        $this->request = $request;
    }
    
    /**
     * Returns InitController
     * 
     * @return InitController
     */
    public function getInitController(): InitController {
        if (is_null($this->initController)) {
            $this->initController = new InitApplicationController();
            $this->handleRequestController = new HandleRequestApplicationController();
//            Switch between 2 above and below lines depending on which pattern you want to use
//            $this->initController = new InitFrontController();
//            $this->handleRequestController = new HandleRequestFrontController();
        }
        
        return $this->initController;
    }
    
    /**
     * Returns HandleRequestController
     * 
     * @return HandleRequestController
     */
    public function getHandleRequestController(): HandleRequestController {
        if (is_null($this->handleRequestController)) {
            $this->initController = new InitApplicationController();
            $this->handleRequestController = new HandleRequestApplicationController();
//            Switch between 2 above and below lines depending on which pattern you want to use
//            $this->initController = new InitFrontController();
//            $this->handleRequestController = new HandleRequestFrontController();
        }
        
        return $this->handleRequestController;
    }
    
    /**
     * Sets the configuration options as defined in the [config] section of the
     * app_options.ini file of the application. 
     * Key = 'config' => Value = content of the [config] section in the app_options.ini file
     * 
     * @param Conf $conf
     */
    public function setAppConfig(Conf $conf): void {
        $this->conf = $conf;
    }
    
    /**
     * Returns the configuration options as defined in the [config] section of the
     * app_options.ini file of the application.
     * 
     * @return Conf Key = 'config' => Value = content of the [config] section in the app_options.ini file
     */
    public function getAppConfig(): Conf {
        if (is_null($this->conf)) {
            $this->conf = new Conf();
        }
        
        return $this->conf;
    }
    
    /**
     * Set the list of commands associated to the Request
     * 
     * @param Conf $commands
     */
    public function setCommands(Conf $commands): void {
        $this->commands = $commands;
    }
    
    /**
     * Returns the list of commands associated to the Request
     * 
     * @return Conf
     */
    public function getCommands(): Conf {
        return $this->commands;
    }
    
    /**
     * Returns LoginManager
     * 
     * @return LoginManager
     */
    function getLoginManager(): LoginManager {
        if (is_null($this->loginManager)) {
            $this->loginManager = new LoginManager();
        }
        
        return $this->loginManager;
    }

    /**
     * Returns PHP Database Object specific to this application
     * 
     * @return \PDO PHP Database Object
     */
    function getDb(): \PDO {
        if (is_null($this->db)) {
            try {
                $this->db = new \PDO("mysql:host="._DBHOST.";dbname="._DBNAME, _DBUSER, _DBPASSWORD);
            } catch (\PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }
        
        return $this->db;
    }
}