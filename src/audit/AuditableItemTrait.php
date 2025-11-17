<?php
/**
 * AuditableItemTrait.php
 *
 * @package controllerframework\audit
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\audit;

/**
 * Trait to implement the methods of the AuditableItem interface
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
trait AuditableItemTrait {
    
    /**
     * Implementation for the notification of the AuditTrace
     * 
     * @param string $functionname Name of the function. Best is to use __FUNCTION__ variable
     * @param array $arglist Contains text you want to add to the function name to clarify what you are auditing
     */
    public function notifyAuditTrace(string $functionname, array $arglist = []): void {
        $auditdescription = $functionname.' |';
        foreach ($arglist as $arg) {
            $auditdescription .= $arg.'; ';
        }
        (new AuditTrace())->notify($this, $auditdescription);
    }
}
