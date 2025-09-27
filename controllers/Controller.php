<?php
/**
 * Controller.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Implementation of a MVC framework. The Controller is the class to init and run the whole framework.
 * 
 * Should be initialized in the index.php in the root of the project:
 * 
 * <?php 
 * 
 * include './controllerFramework/autoload.php';
 * 
 * controllers\Controller::run();
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Controller {
    
    /**
     *
     * @var \registry\Registry Handle to Registry 
     */
    private $reg;

    /**
     * Constructor
     */
    private function __construct() {
        $this->reg = \registry\Registry::instance();
    }

    /**
     * Inits the MVC framework and handles consequently the request
     */
    public static function run(): void {
        $instance = new Controller();
        $instance->init();
        $instance->handleRequest();
    }

    /**
     * Init the MVC framework
     */
    private function init(): void {
        $this->reg->getInitController()->init();
    }

    /**
     * Handles the request sent to the Web server by executing the command linked
     * to the path in the request and render the result of the executed command. 
     * The rendering can be the render of a view, a forward or a data request (downloads 
     * of documents or files or interfaces, mollie callback, ajax request, etc.)
     * 
     * @throws \Exception
     */
    private function handleRequest(): void {
        try {
            $request = $this->reg->getRequest();            
            $this->reg->getHandleRequestController()->handleRequest($request);            
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }
    }
}