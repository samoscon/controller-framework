<?php
/**
 * MemberTypeImplementation.php
 *
 * @package model\members
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\members;

/**
 * Different types of MemberTypeImplementation (e.g. RGLR, PRTN, EXPT, etc.). 
 * Types are defined in the database column type in the table member
 * 
 * Implementation of this class follows the design pattern 'Bridge'
 * 
 * @link ../graphs/members%20Class%20Diagram.svg Members class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class MemberTypeImplementation {
    
    /**
     * Standard behavior is that there is no seperate Membership fee on top of yearly participation fee.
     * 
     * If you want to apply a fix membership fee in addition to participation fee, override this method
     * 
     * @param \model\Member $member Member object
     * @return int
     */
    public function getMembershipFee(\model\Member $member): float {
        return 0;
    }
    
    /**
     * Returns yearly participation fee to the club. Fee can be different per type.
     * 
     * @param \model\Member $member Member object
     * @return float Yearly participation fee
     */
    abstract public function getYearlyParticipationFee(\model\Member $member): int;
    
    /**
     * Returns the pro rata calculated yearly participation fee to the club.
     * 
     * Standard behavior is no ProRata applied to yearly participation fee. 
     * If you want to apply ProRata to the yearly participation fee, override this method.
     * 
     * @param float $fee The full year participation fee
     * @return float Pro rata yearly participation fee
     */
    public function calculateProRataFee(float $fee): float {
        return $fee;
    }
}