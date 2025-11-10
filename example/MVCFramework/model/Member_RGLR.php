<?php
/**
 * Member_RGLR.php
 *
 * @package model
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Specialization of MemberTypeImplementation for Members where type = 'RGLR' (i.e. regular)
 *
 * @link ../graphs/members%20Class%20Diagram.svg Members class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Member_RGLR extends \controllerframework\members\MemberTypeImplementation {

    /**
     * Returns yearly participation fee for this type of Member
     * 
     * @param \model\members\Member $member Member object
     * @return int
     */
    public function getYearlyParticipationFee(\model\Member  $member): int {
        return 350;
    }    
}