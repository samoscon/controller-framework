<?php
/**
 * DatarequestRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Abstract class to render a datarequest towards the browser. The type of datarequest 
 * (agenda, download, mollie, ajax) determines which subclass to return
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class DatarequestRenderComponent implements RenderComponent{
    
    /**
     * Abstract factory to construct the correct Datarequest Render Component
     * 
     * @param string $type
     * @return DatarequestRenderComponent Returns a subclass of the DatarequestRenderComponent
     */
    static public function init(string $type): DatarequestRenderComponent {
        return new('\\controllers\\'.$type.'RenderComponent');
    }
}
