<?php
/**
 * MemberComposite.php
 *
 * @package model\members
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\members;

/**
 * Specific implementation of an Activity tree within client code
 * App
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class MemberComposite extends Member {
    /**
     * @var \db\Objectmap $childeren
     */
    protected $children;

    public function __construct(int $id) {
        parent::__construct($id);
        $this->children = new \controllerframework\db\ObjectMap();
    }

    /**
     * Creates on the basis of a database row the corresponding object in a subclass of Activity
     * 
     * Based on design pattern 'Abstract Factory'
     * 
     * @param array $row
     * @return \model\members\Member
     */
    #[\Override]
    public static function getInstance(array $row): Member {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $member = new $classname($row['id']);
        $member->initProperties($row);
        if($row['parent_id']) {
            $member->parent = $classname::find($row['parent_id']);
        }
        return $member;        
    }

    public function getChildren(): \controllerframework\db\ObjectMap {
        if(!$this->children->valid()) {
            $this->setChildren();
        }
        return $this->children;
    }
    
    private function setChildren(): void {
        $this->children =  self::mapper()->getChildren($this);
    }

    #[\Override]
    public function isComposite(): bool {
        return true;
    }
}
