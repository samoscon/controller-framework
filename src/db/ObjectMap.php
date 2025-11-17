<?php
/**
 * ObjectMap.php
 *
 * @package controllerframework\db
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\db;

/**
 * Extension of SplObjectStorage to get Objects by their id
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class ObjectMap extends \SplObjectStorage {
    
    /**
     * Method for lazy initialization of objects in an ObjectMap
     * 
     * @param int $info Find object on basis of database row id in the ObjectMap (lazy initialisation)
     * @return DomainObject or null
     */
    public function getObjectBy(int $info): ?DomainObject
    {
        $this->rewind();
        while ($this->valid()) {
            $object = $this->current();
            $data = $this->getInfo();
            if ($info === $data) {
                $this->rewind();
                return $object;
            }
            $this->next();
        }

        return null;
    }
}
