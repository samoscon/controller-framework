<?php
/**
 * Request.php
 *
 * @package registry
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace registry;

/**
 * Superclass to handle the Request sent to the Web Server
 *
 * @link ../graphs/registry%20Class%20Diagram.svg Registry class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Request {
    /**
     * Properties of the REQUEST
     *
     * @var array of keys and values
     */
    protected array $properties;
    
    /**
     * Status of the Command
     *
     * @var int 
     */
    protected int $status;
    
    /**
     *
     * @var array of strings 
     */
    protected array $feedback = [];
    
    /**
     *
     * @var string Path of the Request; if no path given, defaults to '/'
     * 
     */
    protected string $path = "/";
    
    /**
     * Constructor
     * 
     * @return Request
     */
    public function __construct() {
        return $this->init();
    }
    
    /**
     * Initialization of the Request
     */
    abstract public function init(): void;
    
    /**
     * Forward of the Request
     */
    abstract public function forward(string $path): void;
    
    /**
     * Setter $path
     * 
     * @param string $path
     */
    public function setPath($path): void {
        $this->path = $path;
    }
    
    /**
     * Returns path
     * 
     * @return string
     */
    public function getPath(): string {
        return $this->path;
    }
    
    /**
     * Returns property with a certain key
     * 
     * @param string $key
     * @return mixed Returns the $value associated with the $key or null if key not found
     */
    public function get(string $key): mixed {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }
        
        return null;
    }
    
    /**
     * Setter of a property in $properties on basis of a key-value pair
     * 
     * @param string $key
     * @param mixed $val
     */
    public function set(string $key, mixed $val): void {
        $this->properties[$key] = $val;
    }
    
    /**
     * Adds a feedback to the $feedback array
     * 
     * @param string $msg
     */
    public function addFeedback(string $msg): void {
        array_push($this->feedback, $msg);
    }
    
    /**
     * Returns $feedback array
     * 
     * @return array
     */
    public function getFeedback(): array {
        return $this->feedback;
    }
    
    /**
     * Returns the $feedback array as a string with line seperators per feedback
     * 
     * @param string $seperator
     * @return string
     */
    public function getFeedbackString(string $seperator = "\n"): string {
        return implode($seperator, $this->feedback);
    }
    
    /**
     * Resets $feedback array to an empty array
     */
    public function clearFeedback(): void {
        $this->feedback = [];
    }
    
    /**
     * Setter of $status
     * 
     * @param int $status
     */
    public function setCmdStatus(int $status): void {
        $this->status = $status;
    }
    
    /**
     * Getter of $status
     * 
     * @return int
     */
    public function getCmdStatus(): int {
        return $this->status;
    }
}