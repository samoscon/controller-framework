<?php
/**
 * Member_PRTN.php
 *
 * @package model
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Specialization of MemberTypeImplementation for Members where type = 'PRTN' (i.e. partner)
 *
 * @link ../graphs/members%20Class%20Diagram.svg Members class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Member_PRTN extends \controllerframework\members\MemberTypeImplementation {

    /**
     * Returns yearly participation fee for this type of Member
     * 
     * When the partner of the member is also a member, 
     * the first paying member of the 2 partners will pay 350, the second 325.
     * 
     * @param model\members\Member $member Member object
     * @return int
     */
    #[\Override]
    public function getYearlyParticipationFee(\model\Member $member): int {
        $reg = \registry\Registry::instance();
        $partner = $reg->getMemberMapper()->find($member->partnerid);    
        return $partner->subscriptionuntil > $member->subscriptionuntil ? 300 : 350;
    }
    
}
