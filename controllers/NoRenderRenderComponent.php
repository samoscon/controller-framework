<?php
/**
 * NoRenderRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Forwards a response to the browser where no rendering is needed (e.g. webhook (callback) from an API service, 
 * download of a PDF document, etc.)
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class NoRenderRenderComponent extends DatarequestRenderComponent {
    
    /**
     * Forwards a response to the browser where no rendering is needed
     * 
     * @param \registry\Request $request
     */
    #[\Override]
    public function render(\registry\Request $request): void {
        exit();
    }

}
