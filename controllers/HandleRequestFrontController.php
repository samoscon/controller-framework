<?php
/**
 * HandleRequestFrontController.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Subclass of HandleRequest
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class HandleRequestFrontController extends HandleRequestController {

    /**
     * Implementation of the handling of a request for the Front Controller Framework
     * 
     * @param \registry\Request $request
     */
    #[\Override]
    public function handleRequest(\registry\Request $request): void {
        $resolver = new CommandResolver();                
        $cmd = $resolver->getCommand($request);
        $cmd->execute($request);
    }
}
