<?php
/**
 * RenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Interface to be implemented by any type of response that needs to be rendered back to the browser/client, 
 * e.g. a view (template), a forward to a new path, a response to a datarequest (ajax, file download), etc., etc.
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
interface RenderComponent {
    
    /**
     * Interface method for specific implementation of the render
     * 
     * @param \registry\Request $request
     */
    public function render(\registry\Request $request): void;
}
