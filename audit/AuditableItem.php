<?php
/**
 * AuditableItem.php
 *
 * @package audit
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace audit;

/**
 * Interface that needs to be implemented by classes that you want to audit/log.
 * 
 * When you implement this interface you have to use the trait AuditableItemTrait at beginning of your class definition.
 * Add following line at the beginning of the class definition:
 * 
 * use \audit\AuditableItemTrait;
 * 
 * Within the method(s) that you want to audit of that class, you add a line :
 * 
 * $this->notifyAuditTrace(__FUNCTION__,['xyz']);
 * OR
 * $this->notifyAuditTrace(__FUNCTION__, func_get_args());
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
interface AuditableItem {
    /**
     * Function needs to be added in the class implementing the interface. 
     * This can however be achieved by using the AuditableItemTrait in the class definition.
     * 
     * @param string $functionname Name of the function. Best is to use __FUNCTION__ variable
     * @param array $arglist Contains text you want to add to the functionname to clarify what you are auditing
     */
    public function notifyAuditTrace(string $functionname, array $arglist = []): void;
}
