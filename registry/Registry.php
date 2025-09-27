<?php
/**
 * Registry.php
 *
 * @package registry
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace registry;

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
     * @var \controllers\InitController Handle to help the class that will initialize 
     * the Registry with App config info and commands
     */
    private ?\controllers\InitController $initController = null;
    
    /**
     *
     * @var \controllers\HandleRequestController Handle to help the class that will initialize 
     * the Registry with App config info and commands
     */
    private ?\controllers\HandleRequestController $handleRequestController = null;
    
    /**
     *
     * @var \controllers\Conf Helper class for handling Configurations
     */
    private ?\controllers\Conf $conf = null;
    
    /**
     *
     * @var \controllers\Conf of Commands 
     */
    private ?\controllers\Conf $commands = null;
    
    /**
     *
     * @var \sessions\LoginManager Handle to the LoginManager facade
     */
    private ?\sessions\LoginManager $loginManager = null;
    
    /**
     *
     * @var \model\MemberMapper Handle to the application specific MemberMapper
     */
    private ?\model\MemberMapper $memberMapper = null;
    
    /**
     *
     * @var \model\ActivityMapper Handle to the application specific ActivityMapper
     */
    private ?\model\ActivityMapper $activityMapper = null;
    
    /**
     *
     * @var \model\ActivityMapper Handle to the application specific ActivityMapper
     */
    private ?\model\activities\CostitemMapper $costitemMapper = null;
    
    /**
     *
     * @var \model\SubscriptionMapper Handle to the SubscriptionMapper
     */
    private ?\model\SubscriptionMapper $subscriptionMapper = null;
    
    /**
     *
     * @var \model\PaymentMapper Handle to the PaymentMapper
     */
    private ?\model\PaymentMapper $paymentMapper = null;
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
     * Reset van het object
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
     * @param \registry\Request $request
     */
    public function setRequest(\registry\Request $request): void {
        $this->request = $request;
    }
    
    /**
     * Returns InitController
     * 
     * @return \controllers\InitController
     */
    public function getInitController(): \controllers\InitController {
        if (is_null($this->initController)) {
            $this->initController = new \controllers\InitApplicationController();
            $this->handleRequestController = new \controllers\HandleRequestApplicationController();
//            Switch between 2 above and below lines depending on which pattern you want to use
//            $this->initController = new \controllers\InitFrontController();
//            $this->handleRequestController = new \controllers\HandleRequestFrontController();
        }
        
        return $this->initController;
    }
    
    /**
     * Returns InitController
     * 
     * @return \controllers\InitController
     */
    public function getHandleRequestController(): \controllers\HandleRequestController {
        if (is_null($this->handleRequestController)) {
            $this->initController = new \controllers\InitApplicationController();
            $this->handleRequestController = new \controllers\HandleRequestApplicationController();
//            Switch between 2 above and below lines depending on which pattern you want to use
//            $this->initController = new \controllers\InitFrontController();
//            $this->handleRequestController = new \controllers\HandleRequestFrontController();
        }
        
        return $this->handleRequestController;
    }
    
    /**
     * Sets the configuration options as defined in the [config] section of the
     * app_options.ini file of the application. 
     * Key = 'config' => Value = content of the [config] section in the app_options.ini file
     * 
     * @param \controllers\Conf $conf
     */
    public function setAppConfig(\controllers\Conf $conf): void {
        $this->conf = $conf;
    }
    
    /**
     * Returns the configuration options as defined in the [config] section of the
     * app_options.ini file of the application.
     * 
     * @return \controllers\Conf Key = 'config' => Value = content of the [config] section in the app_options.ini file
     */
    public function getAppConfig(): \controllers\Conf {
        if (is_null($this->conf)) {
            $this->conf = new \controllers\Conf();
        }
        
        return $this->conf;
    }
    
    /**
     * Set the list of commands associated to the Request
     * 
     * @param \controllers\Conf $commands
     */
    public function setCommands(\controllers\Conf $commands): void {
        $this->commands = $commands;
    }
    
    /**
     * Returns the list of commands associated to the Request
     * 
     * @return \controllers\Conf
     */
    public function getCommands(): \controllers\Conf {
        return $this->commands;
    }
    
    /**
     * Returns LoginManager
     * 
     * @return \sessions\LoginManager
     */
    function getLoginManager(): \sessions\LoginManager {
        if (is_null($this->loginManager)) {
            $this->loginManager = new \sessions\LoginManager();
        }
        
        return $this->loginManager;
    }

    /**
     * Returns MemberMapper specific to the application
     * 
     * @return \model\MemberMapper
     */
    function getMemberMapper(): \model\MemberMapper {
        if (is_null($this->memberMapper)) {
            $class = '\\model\\MemberMapper';
            $this->memberMapper = new $class();
        }
        
        return $this->memberMapper;
    }

    /**
     * Returns MemberMapper specific to the application
     * 
     * @return \model\MemberMapper
     */
    function getMemberCompositeMapper(): \model\MemberMapper {
        return $this->getMemberMapper();
    }

    /**
     * Returns ActivityMapper specific to the application
     * 
     * @return \model\ActivityMapper
     */
    function getActivityMapper(): \model\ActivityMapper {
        if (is_null($this->activityMapper)) {
            $class = '\\model\\ActivityMapper';
            $this->activityMapper = new $class();
        }
        
        return $this->activityMapper;
    }

    /**
     * Returns ActivityMapper specific to the application
     * 
     * @return \model\ActivityMapper
     */
    function getActivityCompositeMapper(): \model\ActivityMapper {
        return $this->getActivityMapper();
    }

    /**
     * Returns CostitemMapper specific to the application
     * 
     * @return \model\activities\CostitemMapper
     */
    function getCostitemMapper(): \model\CostitemMapper {
        if (is_null($this->costitemMapper)) {
            $class = '\\model\\CostitemMapper';
            $this->costitemMapper = new $class();
        }
        
        return $this->costitemMapper;
    }

    /**
     * Returns SubscriptionMapper
     * 
     * @return \model\SubscriptionMapper
     */
    function getSubscriptionMapper(): \model\SubscriptionMapper {
        if (is_null($this->subscriptionMapper)) {
            $this->subscriptionMapper = new \model\SubscriptionMapper();
        }
        
        return $this->subscriptionMapper;
    }

    /**
     * Returns SubscriptionValidator specific to the application
     * 
     * @param string $level can be 'User' (default) or 'Admin' or any other level if a subclass is available for it
     * @return \model\subscriptions\SubscriptionValidationStrategy Concrete subclass
     */
    function getSubscriptionValidator(string $level = 'User'): \model\subscriptions\SubscriptionValidationStrategy {
        $class = '\\model\\SubscriptionValidation'.$level;
        return new $class();
    }

    /**
     * Returns PaymentMapper
     * 
     * @return \model\PaymentMapper
     */
    function getPaymentMapper(): \model\PaymentMapper {
        if (is_null($this->paymentMapper)) {
            $this->paymentMapper = new \model\PaymentMapper();
        }
        return $this->paymentMapper;
    }

    /**
     * Returns PHP Database Object specific to this application
     * 
     * @return PDO PHP Database Object
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