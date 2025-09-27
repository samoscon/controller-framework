<?php
/**
 * Conf.php
 *
 * @package controllers
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllers;

/**
 * Transforming a named array in an object
 *
 * @link ../graphs/controllers%20(Application%20Controller)%20Class%20Diagram.svg Controllers class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Conf {
    
    /**
     *
     * @var Conf Object simulating an array
     */
    private array $conf;
    
    /**
     * Constructor
     * 
     * @param array $conf Named array
     */
    public function __construct(array $conf = array()) {
        $this->conf = $conf;
    }
    
    /**
     * Sets an element in the Conf object
     * 
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void {
        $this->conf[$key] = $value;
    }
    
    /**
     * Returns a value of the Conf object
     * 
     * @param string $key
     * @return mixed Or returns null if the key is not known
     */
    public function get(string $key): mixed {
        if (!isset($this->conf[$key])) {
            return null;
        }
        
        return $this->conf[$key];
    }
}