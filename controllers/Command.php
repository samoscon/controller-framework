<?php
/**
 * Command.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Implementation of design pattern 'Command'
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Command {
    
    /**
     *
     * @var \registry\Registry Handle to Registry 
     */
    protected  \registry\Registry $reg;
    
    /**
     *
     * @var \sessions\Login Handle to LoginManager 
     */
    protected \sessions\Login $loginLevel;

    const   CMD_DEFAULT = 0,
            CMD_OK = 1,
            CMD_ERROR = 2,
            CMD_INSUFFICIENT_DATA = 3,
            CMD_ADMIN = 4,
            CMD_CHANGE_PASSWORD = 5,
            CMD_CONTINUE = 6;

    /**
     * Constructor
     */
    final public function __construct() {
        $this->reg = \registry\Registry::instance();
    }
    
    /**
     * Returns a status as defined in the constants reflecting the status of the execution.
     * 
     * @param \registry\Request $request
     * @return int Returns 1 of the constants of this class
     */
    public function execute(\registry\Request $request): int {
        $this->getLevelOfLoginRequired();
        if ($this->loginLevel->validate()) {
            $status = $this->doExecute($request);
        } else {
            $request->addFeedback('Niet meer ingelogd. Log opnieuw in aub.');
            $params= $request->get("id") ? '?id='.$request->get("id") : '';
            $originalPath = $request->getPath().$params;
            setcookie('originalPath', $originalPath, time() + 120, '/');
            $status = self::CMD_ERROR;            
        }
        $request->setCmdStatus($status);
        return $status;
    }
    
    /**
     * Set a login level for the command (e.g. none, user, admin, etc.).
     * 
     * The login level is determined in one of the subclasses of Login. 
     * Implements design pattern 'Strategy'
     * 
     * @param \sessions\Login login
     */
    protected function setLoginLevel(\sessions\Login $loginLevel): void {
        $this->loginLevel = $loginLevel;
    }

    /**
     * Overrides the forward after the login of a user
     * 
     * @param \members\Member $user
     * @return int Returns a status
     */
    protected function loginChecks(\model\members\Member $user): int {
        if(!$user->ownpwd){
            return self::CMD_CHANGE_PASSWORD;
        }

        if($user->role == 'A'){
            return self::CMD_ADMIN;
        }

        return self::CMD_OK;        
    }
    
    /**
     * Helper method to construct a response in the Request
     * 
     * @param \registry\Request $request
     * @param array $responses Named array
     */
    protected function addResponses(\registry\Request $request, $responses): void {
        foreach ($responses as $key => $value) {
            $request->set($key, $value);
        }
    }
    
    /**
     * Abstract function to be specialized in the subclass of Command
     * 
     * @param \registry\Request $request
     */
    abstract public function doExecute(\registry\Request $request): int;
    
    /**
     * Abstract function to be specialized in the subclass of Command
     */
    abstract protected function getLevelOfLoginRequired(): void;
}