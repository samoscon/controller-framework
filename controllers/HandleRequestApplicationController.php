<?php
/**
 * HandleRequestApplicationController.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Subclass of HandleRequestController
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class HandleRequestApplicationController extends HandleRequestController {

    /**
     * Implementation of the handling of a request for the Application Controller Frameworl
     * 
     * @param \registry\Request $request
     */
    #[\Override]
    public function handleRequest(\registry\Request $request): void {
        $this->getCommand($request)->execute($request);      
        $this->getRenderer($request)->render($request);
    }
}
