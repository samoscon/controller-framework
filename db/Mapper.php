<?php
/**
 * Mapper.php
 *
 * @package db
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace db;

/**
 * Superclass for the 'specialized' Mappers. 1 specialized Mapper per underlying domain class
 *
 * @version 4.0
 */
abstract class Mapper implements \audit\AuditableItem {
    use \audit\AuditableItemTrait;
    
    /**
     *
     * @var PDO PHP Database Object, connecting to the database of the application 
     */
    protected \PDO $db;
    
    /**
     *
     * @var ObjectMap Collection of objects already retrieved from database and instantiated as object 
     */
    protected ObjectMap $collection;
    
    /**
     * Constructor
     * 
     */
    function __construct() {
        $reg = \registry\Registry::instance();
        $this->db = $reg->getDb();
        $this->collection = new \db\ObjectMap();
    }
    
    /**
     * Returns a domain object on the basis of its database row id
     * 
     * @param int $id Database row id
     * @return DomainObject The object
     * @throws \Exception Exception thrown if the $id can not be found in the database
     */
    public function find(string $classname, int $id): DomainObject {
        $object = $this->collection->getObjectBy($id);
        if($object) {
            return $object;
        }
        $sql = $this->db->prepare("SELECT * FROM {$this->tablename()} WHERE id=?");
        $sql->execute([$id]);
        $row = $sql->fetch();
        $sql->closeCursor();
        
        if(! is_array($row)) {
            throw new \Exception("{$this->tablename()} met id $id bestaat niet.");
        }
        
        $object = $this->createObject($classname, $row);
        $this->collection->attach($object, $row['id']);
        
        return $object;
    }
    
    /**
     * Returns a collection of objects for all rows in a table (note that 1 element of selection can be added)
     * 
     * @param string $selectclause Element of selection
     * @return \db\ObjectMap of DomainObjects
     */
    public function findAll(string $classname, string $selectclause = ''): \db\ObjectMap {
        $domainobjects = new \db\ObjectMap();    
        $sql = $this->db->prepare("SELECT * FROM {$this->tablename()} ".$selectclause);
        $sql->execute();
        $result = $sql->fetchAll();
        $sql->closeCursor();

        foreach($result as $row) {
            $object = $this->doCreateObject($classname, $row);
            $domainobjects->attach($object, $row['id']);
        }
        return $domainobjects;        
    }


    /**
     * Returns an object
     * 
     * @param array $row Named array with columnnames and values 
     * @return DomainObject
     */
    public function createObject(string $classname, array $row): DomainObject {
        return $this->doCreateObject($classname, $row);
    }
    
    /**
     * Inserts an object into the database
     * 
     * @param array $properties
     * @return \db\DomainObject Returns the newly created Object in the database
     */
    public function insert(string $classname, array $properties): \db\DomainObject {
        $this->db->exec("INSERT INTO {$this->tablename()} (`description`) VALUES ('TBD')");
        $newid = $this->db->lastInsertId();
        $newobject = $this->find($classname, $newid);
        $this->update($newobject, $properties);
        return $this->find($classname, $newid);
    }
    
    /**
     * Updates the columns of the database row related to the $obj 
     * as given in the named array of columnnames and values
     * 
     * @param \db\DomainObject $obj
     * @param array $updates Named array of columnnames and values
     */
    public function update(\db\DomainObject $obj, array $updates): void {
        $this->db->beginTransaction();
        foreach ($updates as $field => $value) {
            $this->db->exec("UPDATE  {$this->tablename()} SET $field = '$value' WHERE id = {$obj->getId()}");
        }
        $this->db->commit();
    }
    
    /**
     * Deletes the row of the related $obj in the database
     * 
     * @param \db\DomainObject $obj
     */
    public function delete(\db\DomainObject $obj): void {
        $this->db->exec("DELETE FROM {$this->tablename()} WHERE id = {$obj->getId()}");
    }
    
    public function checkForChildren(int $id): array {
        $sql = $this->db->prepare("SELECT * FROM {$this->tablename()} WHERE parent_id = ?");
        $sql->execute([$id]);
        $result = $sql->fetchAll();
        $sql->closeCursor();
        
        return $result;
    }
    
    /**
     * Returns the name of the associated table name in the specialized subclass
     * 
     * @return string Name of the associated table
     */
    abstract protected function tablename(): string;
    
    /**
     * Returns the specialized DomainObject in the specialized subclass
     * 
     * @param array $row Named array of columnnames and values
     * @return \db\DomainObject Subclass of DomainObject
     */
    abstract protected function doCreateObject(string $classname, array $row): DomainObject;
    
}