<?php
/**
 * DomainObject.php
 *
 * @package db
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace db;

/**
 * Superclass managing any object that is stored in the database
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class DomainObject {
    
    /**
     *
     * @var int Database row id (each table in the database must have an id column which is the unique key of that table)
     */
    private int $id;
    
    /**
     *
     * @var controllers\Conf properties. Array with the properties of the table that are not defined in the controllerFramework
     */
    public ?\controllers\Conf $properties = null;
    
    /**
     * Constructor
     * 
     * @param int $id Database row id to link the object to the database row
     */
    public function __construct(int $id) {
        $this->id = $id;
    }
    
    /**
     * Returns an object on the basis of the data in the array of data ($row) provided as param
     * 
     * @param array $row Array of named data fields used to fill up the object properties
     */
    abstract public static function getInstance(array $row): \db\DomainObject;
    
    
    /**
     * Find an object on the basis of the id in the database through the respective mapper of the calling class
     * 
     * @param int $id id in the database
     * @return \db\DomainObject
     */
    public static function find(int $id): \db\DomainObject {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        return self::mapper()->find($classname, $id);
    }
    
    /**
     * Find a collection of objects on the basis of a select clause. If the select clause is empty, it returns a SELECT * FROM {tablename}
     * where the tablename is retrieved from the mapper linked to the calling class
     * 
     * @param string $selectclause 
     * @return \db\ObjectMap
     */
    public static function findAll(string $selectclause = ''): \db\ObjectMap {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        return self::mapper()->findAll($classname, $selectclause);
    }
    
    /**
     * Insert an object on the basis of an array of properties in the database through the respective mapper of the calling class
     * 
     * @param array $properties
     * @return \db\DomainObject
     */
    public static function insert(array $properties): \db\DomainObject {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        return self::mapper()->insert($classname, $properties);
    }
    
    /**
     * Update an object on the basis of an array of properties in the database through the respective mapper of the calling class
     * 
     * @param array $properties
     */
    public function update(array $properties): void {
        self::mapper()->update($this, $properties);
    }
    
    /**
     * Update an object in the database through the respective mapper of the calling class
     * 
     */
    public function delete(): void {
        if($this->isComposite()) {
            throw new \Exception((new \ReflectionClass(get_called_class()))->getName() . " can not be deleted.");
        }
        
        self::mapper()->delete($this);        
    }
    
    /**
     * Helper method to get the correct mapper on the basis of the name of the calling class
     * 
     * @param array $properties
     */
    protected static function mapper(): \db\Mapper {
        $reg = \registry\Registry::instance();
        $methodname ='get'.(new \ReflectionClass(get_called_class()))->getShortName().'Mapper'; 
        return $reg->$methodname();
    }
    
    /**
     * You can provide a method that lets the client code figure out whether a
     * component can bear children.
     */
    public function isComposite(): bool {
        return false;
    }

    /**
     * Return id
     * 
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set properties keys without values. The values will be set by Interceptor __set()
     * 
     * @var array $properties
     */
    private function setProperties(array $properties): void {
        $conf = new \controllers\Conf();
        foreach (array_keys($properties) as $property) {
            if($property !== 'id' && is_string($property)) {
                $conf->set($property, null);
            }
        }
        $this->properties = $conf;
    }
    
    /**
     * Set properties keys with values on the basis of a row as array. Each key 
     * in the row (except the id) will be set in the properties.
     * 
     * @var array $row
     */
    protected function initProperties(array $row): void {
        $this->setProperties($row);
        foreach (array_keys($row) as $property) {
            if($property !== 'id' && is_string($property)) {
                $this->$property = ($row[$property]);
            }
        }
    }


    /**
     * Get a property that is not explicitly named on the basis of a key that is set by the Interceptor __set()
     * 
     * @var string $key
     * @return mixed Returns the value associated with the key in the properties attribute
     */
    public function __get(string $key): mixed {
        return $this->properties->get($key);
    }
    
    /**
     * Set a property that is not explicitly named on the basis of a key. Implementation 
     * of the Interceptor __set() principle
     * 
     * @var string $key
     * @var mixed $value Value associated with the key
     */
    public function __set(string $key, mixed $value): void {
            $this->properties->set($key, $value);
    }
}
