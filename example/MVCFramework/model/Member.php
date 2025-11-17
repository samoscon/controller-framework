<?php
/**
 * Member.php
 *
 * @package model
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Specialization of Member in the client code
 *
 * @link ../graphs/members%20Class%20Diagram.svg Members class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Member extends \controllerframework\members\Member {
//    put your code here
    /**
     * Initializes a new password for a Member in the database. The Member will receive a mail confirming his new password.
     * 
     * @param string $pwd The generated password
     * @return string Body of the mail with the password sent to the Member
     */
    public function initiatePassword(string $pwd = ''): string {
        $app = APP;
return <<<_MAIL_
Beste $this->name,<br>
<br>       
Uw nieuw paswoord is <b>$pwd</b><br>
<br>
Dit paswoord kan je in de komende 24h slechts 1 maal gebruiken om in te loggen via het login scherm.<br>
<br>
Wanneer je in het scherm <b>Paswoord aanpassen</b> bent, zal je gevraagd worden om een eigen paswoord op te geven.<br>
<br>
Met vriendelijke groet,<br>
Het $app bestuur
_MAIL_;
        
    }
}
