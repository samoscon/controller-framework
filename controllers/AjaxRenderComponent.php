<?php
/**
 * AjaxRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Renders an Ajax Response to the browser in json format
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class AjaxRenderComponent extends DatarequestRenderComponent {
    
    /**
     * Renders an ajax response to the browser in json format
     * 
     * @param \registry\Request $request The $request must contain a Conf with 'results'. 
     * 
     * The 'results' Conf value is in the format of a named array.
     */
    #[\Override]
    public function render(\registry\Request $request): void {
        if (gettype($request->get('results')) !== "array") {
            $request->set('results', array('No results'));
        }
        
        header('Content-Type: application/json');
        print json_encode($request->get('results'));                        
    }
}
