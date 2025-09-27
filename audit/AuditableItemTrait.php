<?php
/**
 * AuditableItemTrait.php
 *
 * @package audit
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace audit;

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
     * @param array $arglist Contains text you want to add to the functionname to clarify what you are auditing
     */
    public function notifyAuditTrace(string $functionname, array $arglist = []): void {
        $auditdescription = $functionname.' |';
        foreach ($arglist as $arg) {
            $auditdescription .= $arg.'; ';
        }
        (new AuditTrace())->notify($this, $auditdescription);
    }
}
