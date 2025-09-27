<?php
/**
 * MollieRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Renders a forward to Mollie payment screen in the browser
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
* @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class MollieRenderComponent extends DatarequestRenderComponent {
    
    /**
     * Renders a forward to Mollie payment screen in the browser
     * 
     * @param \registry\Request $request Must contain 'results'
     */
    #[\Override]
    public function render(\registry\Request $request): void {
        header("Location: " . $request->get('results'));
    }
}