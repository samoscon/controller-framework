<?php
/**
 * DownloadRenderComponent.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Renders a xls file download to the browser
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class DownloadRenderComponent extends DatarequestRenderComponent {
    
    /**
     * Renders xls download to the browser
     * 
     * @param \registry\Request $request Request must contain filename, columnNames, and results
     */
    #[\Override]
    public function render(\registry\Request $request): void {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $request->get('filename'));
        header('Pragma: no-cache');
        header("Expires: 0");

        $outstream = fopen("php://output", "w");
        $line = ($request->get('columnNames') . PHP_EOL);
        fwrite($outstream, $line);

        foreach ($request->get('results') as $line) {
            fputcsv($outstream, $line, ";");
        }
        fclose($outstream);
        exit();        
    }
}