<?php
/**
 * MemberComposite.php
 *
 * @package controllerframework\members
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\members;

use controllerframework\db\ObjectMap;

/**
 * Specific implementation of an Activity tree within client code
 * App
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class MemberComposite extends Member {
    /**
     * @var ObjectMap $childeren
     */
    protected ObjectMap $children;

    public function __construct(int $id) {
        parent::__construct($id);
        $this->children = new ObjectMap();
    }

    /**
     * Creates on the basis of a database row the corresponding object in a subclass of Activity
     * 
     * Based on design pattern 'Abstract Factory'
     * 
     * @param array $row
     * @return Member
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

    /**
     * Returns the childeren of a Composite Member
     * 
     * @return ObjectMap with the Members that are childeren of this Composite Member
     */
    public function getChildren(): ObjectMap {
        if(!$this->children->valid()) {
            $this->setChildren();
        }
        return $this->children;
    }
    
    /**
     * Sets the childeren of a Composite Member
     * 
     */
    private function setChildren(): void {
        $this->children =  self::mapper()->getChildren($this);
    }

    /**
     * Returns whether a Member is Composite or not
     * 
     * @return bool
     */
    #[\Override]
    public function isComposite(): bool {
        return true;
    }

    /**
     * Returns a mail body. In case of a composite, a password can not be initialized.
     * 
     * @param string $pwd The generated password
     * @return string Body of the mail with the password sent to the Member
     */
    #[\Override]
    public function initiatePassword(string $pwd = ''): string {
        return 'Password can only be provided to individual members and not to composites. Contact the web administrator.';
    }
}
