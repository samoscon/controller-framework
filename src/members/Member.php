<?php
/**
 * Member.php
 *
 * @package model\members
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\members;

/**
 * Represents an individual Member of a Club/Organisation that organises Activities.
 * 
 * A member can take part to these activities by subscribing to these activities.
 * 
 * @link ../graphs/members%20Class%20Diagram.svg Members class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class Member extends \controllerframework\db\DomainObject {
    
    /**
     *
     * @var MemberTypeImplementation  Relates the member to a certain member type with a specific implementation.
     * 
     * Based on design pattern 'Builder'
     */
    public ?MemberTypeImplementation $membertypeimplementation = null;
    
    public function __construct(int $id) {
        parent::__construct($id);
    }

    /**
     * Creates on the basis of a database row the corresponding object in a subclass of Member
     * 
     * Based on design pattern 'Abstract Factory'
     * 
     * @param array $row
     * @return \model\Member
     */
    #[\Override]
    public static function getInstance(array $row): Member {
        $classname = '\\'.(new \ReflectionClass(get_called_class()))->getName();
        $children = self::mapper()->checkForChildren($row['id']);
        if($children) {
           return MemberComposite::getInstance($row);
        }
        $member = new $classname($row['id']);
        $member->initProperties($row);
        $membertypeclassname = $classname.'_'.$row['classification'];
        $member->membertypeimplementation = new $membertypeclassname();
        if($row['parent_id']) {
            $member->parent = $classname::find($row['parent_id']);
        }
        return $member;        
    }

    /**
     * Returns yearly fee applicable to this Member
     * 
     * @return float Yearly fee amount (eventually pro rata calculated)
     */
    public function getYearlyfee(): float {
        $fee = $this->membertypeimplementation->getYearlyParticipationFee($this);
        return $this->membertypeimplementation->calculateProRataFee($fee);
    }

    /**
     * Returns FALSE if the member is no longer active
     * 
     * @return boolean
     */
    public function isRejected(): bool {
        return $this->active === 0;
    }
    
    /**
     * Checks whether member has Administrator rights
     * 
     * @return boolean
     */
    public function isAdministrator(): bool {
        return $this->role === 'A';
    }
    
    /**
     * If a Member is within 3 months of the end of his 'subscription until' date, the method will return TRUE
     * 
     * @return boolean
     */
    public function shouldExtendMembership(): bool {
        return (new \DateTime('today + 2 months'))->format('Y-m-d') > $this->subscriptionuntil;
    }

    /**
     * Updates the subscriptionuntil date to 31/12 of this year (in case it is extended before 1/9) 
     * or the next year (in case it is extended as of 1/9)
     * 
     */
    public function extendMembership():void {
        $extendedYear = date('Y');
        if(date('n') > 8) {
            $extendedYear += 1;
        }
        $sqldate = ($extendedYear) * 10000 + 1231;
        self::mapper()->update($this, array('subscriptionuntil' => $sqldate));
    }
    
    /**
     * returns the yearly fee for the member. Amount can be dependent on the classification of the member.
     * 
     * Based on the design pattern 'Builder'
     * 
     * @return float
     */
    protected function getMembershipFee(): float {
        return $this->membertypeimplementation->getMembershipFee($this);
    }    
    
    /**
     * Returns true if the current date >= duedate of the Activity
     * 
     * @return boolean
     */
    public function subscriptionPeriodOver(): bool {
        $duedate = date('Y-m-d', strtotime(str_replace("/", "-", $this->duedate)));
        return $duedate < date('Y-m-d');
    }
    
    /**
     * Returns the effective paid amount for this Member or MemberComposite
     * 
     * @return float Returns an array with a subject and a body
     */
    public function getTotalAmountReceived(): float {
        $total = 0;
        foreach ($this->payments as $payment) {
            if ($payment->status === 'paid') {
                $total += $payment->amount;
            }
        }
        return $total;
    }
}
