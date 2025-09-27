<?php
/**
 * AuditTrace.php
 *
 * @package audit
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace audit;

/**
 * Observer class for a simple logging mechanism
 * 
 * The AuditTrace will write a notified message from an AuditableItem (i.e. any class 
 * that implements the interface of AuditableItem) into a file 'Includes/logfile.txt'
 * 
 * Implements the design pattern 'Observer'
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class AuditTrace {
    /**
     * Writes notification in the logfile.txt
     * 
     * @param \audit\AuditableItem $auditibleItem Object that implements the interface AuditableItem
     * @param string $auditdescription Message to be written to the logfile
     */
    public function notify(AuditableItem $auditibleItem, string $auditdescription): void {
        $user = \sessions\User::getInstance();
        $username = $user ? $user->name .' '. $user->lastname : 'Onbekend';
        $text = $username .' |'.
                get_class($auditibleItem) .' |'
                . $auditdescription.' |'
                . date(DATE_RFC850)
                . PHP_EOL;
        $filename = realpath("./") . "/assets/logging/logfile.txt";
        $myfile = fopen($filename, "a") or die("Unable to open file!");
        fwrite($myfile, $text);
        fclose($myfile);
    }
}
