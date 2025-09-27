<?php
/**
 * AgendaRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Renders an Agenda entry to the browser
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class AgendaRenderComponent extends DatarequestRenderComponent {
    
    /**
     * Renders an agenda entry to the browser
     * 
     * @param \registry\Request $request The $request must contain a Conf with 'filename'
     * and a Conf with 'results'. 
     * The filename will be suffixed with .ics
     * 
     * The results is in the format of an Agenda entry, i.e. 
     * "BEGIN:VCALENDAR
     * ....
     * END:VCALENDAR"
     */
    #[\Override]
    public function render(\registry\Request $request): void {
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$request->get('filename').'.ics');
        echo $request->get('results');        
    }
}