<?php
/**
 * AuditTrace.php
 *
 * @package controllerframework\audit
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */

namespace controllerframework\audit;

use controllerframework\registry\Registry;

/**
 * Observer class for a simple logging mechanism
 *
 * The AuditTrace will write a notified message from an AuditableItem
 * into a logfile.
 *
 * Implements the design pattern 'Observer'
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class AuditTrace {

    /**
     * Writes notification in the logfile.txt
     *
     * @param AuditableItem $auditibleItem Object that implements the interface AuditableItem
     * @param string $auditdescription Message to be written to the logfile
     */
    public function notify(
        AuditableItem $auditibleItem,
        string $auditdescription
    ): void {

        $user = \controllerframework\sessions\User::getInstance();

        $username = $user
            ? $user->name . ' ' . $user->lastname
            : 'Onbekend';

        $text = $username . ' |'
                . get_class($auditibleItem) . ' |'
                . $auditdescription . ' |'
                . date(DATE_RFC850)
                . PHP_EOL;

        $config = Registry::instance()->getAppConfig();
        
        $applicationRoot = $config->get('applicationRoot');
        $loggingPath = $config->get('loggingpath');

        if (empty($loggingPath)) {
            error_log('AuditTrace: loggingpath is not configured.');
            return;
        }

        $filename = rtrim($applicationRoot, DIRECTORY_SEPARATOR)
                    . DIRECTORY_SEPARATOR
                    . trim($loggingPath, DIRECTORY_SEPARATOR)
                    . DIRECTORY_SEPARATOR
                    . 'logfile.txt';
        
        $myfile = fopen($filename, 'a');

        if ($myfile === false) {
            error_log(
                'AuditTrace: unable to open logfile: ' . $filename
            );
            return;
        }

        fwrite($myfile, $text);
        fclose($myfile);
    }
}