<?php
/**
 * MemberMapper.php
 *
 * @package controllerframework\members
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\members;

use controllerframework\db\ObjectMap;

/**
 * Instantiation of the DB Mapper for Member class
 * 
 * Has to be further subclassed for application specific behavior
 *
 * @link ../graphs/members%20Class%20Diagram.svg Members class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class MemberMapper extends \controllerframework\db\Mapper {
    /**
     * @var string Contains the name of the related table to the Member class(es) in the database
     */
    private string $tablename = 'member';
    
    /**
     * Returns the associated tablename for the Member class; i.e. member
     * 
     * @return string
     */
    #[\Override]
    public function tablename(): string {
        return $this->tablename;
    }
    
    /**
     * Returns on the basis of a database row the associated object
     * 
     * @param string $classname
     * @param array $row
     * @return Member
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): Member {
        return $classname::getInstance($row);
         
    }   
    
    /**
     * Returns the childeren of a Composite Member
     * 
     * @param MemberComposite $membercomposite 
     * @return ObjectMap with the Members that are childeren of this Composite Member
     */
    public function getChildren(MemberComposite $membercomposite): ObjectMap {
        $result = $this->checkForChildren($membercomposite->getId());
        
        $memberchildren = new ObjectMap();
        foreach ($result as $row) {
            $member = \model\Member::find($row['id']);
            $memberchildren->attach($member, $row['id']);
        }
        return $memberchildren;
    }
}