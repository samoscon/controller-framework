<?php
/**
 * LoginManager.php
 *
 * @package sessions
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace sessions;

/**
 * Manages the login (including management of usernames and passwords) and the logout of a User in a session. 
 * Each password change and login will be notified to an audit trace.
 * 
 * @link ../graphs/sessions%20Class%20Diagram.svg Sessions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class LoginManager implements \audit\AuditableItem {
    use \audit\AuditableItemTrait;
    
    /**
     *
     * @var PDO Handle to application database 
     */
    protected ?\PDO $db = null;
    
    /**
     *
     * @var int Max nbr of allowed minutes since last active session
     */
    protected int $lastActive;
    
    /**
     *
     * @var int Random initialized integer for hashing the password 
     */
    protected int $rand;
    
    /**
     *
     * @var User Member in the session 
     */
    protected ?\model\members\Member $user = null;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = \registry\Registry::instance()->getDb();
        $this->lastActive = 15;
        $this->rand = _RAND;
        session_start();
        if(isset($_SESSION[APP.'_memberID'])){
            $memberid = $_SESSION[APP.'_memberID'];
            $this->user = User::getInstance($memberid);
        }
    }

    /**
     * Returns User of the session as a Member object
     * 
     * @return User or Null
     */
    public function getUser(): ?\model\members\Member {
        return $this->user;
    }

    /**
     * Returns the database row id from the Member on the basis of his mail address
     * 
     * @param string $username
     * @return int Database row id or null in case the row was not found or if member is not active
     */
    public function validateUsername(string $username): int|false {
        $sql = $this->db->prepare("SELECT id FROM member WHERE email = ? AND active = '1'");
        $sql->execute([$username]);
        $row = $sql->fetch();
        $sql->closeCursor();

        return $row ? $row['id'] : false;
    }
    
    /**
     * Checks the password of the Member during login
     * 
     * @param int $memberid Database row id of the Member
     * @param string $password Password as provided by the Member
     * @return boolean
     */
    public function validatePassword(int $memberid, string $password): bool {
        $sql = $this->db->prepare("SELECT password FROM member WHERE id = ? LIMIT 1");
        $sql->execute([$memberid]);
        $row = $sql->fetch();
        $sql->closeCursor();

        $pwd = $row['password'];

        // The first 64 characters of the hash is the salt
        $salt = substr($pwd, 0, 64);
        $hash = $salt . $password;

        // Hash the password as we did before
        for ($i = 0; $i < $this->rand; $i ++) {
            $hash = hash('sha256', $hash);
        }

        $hash = $salt . $hash;
        return ($hash == $pwd);
    }
    
    /**
     * Creates SESSION after a successful login 
     * 
     * @param int $memberid Database row id of the Member
     * @param boolean $keepLoggedin Default =  true
     * @param int $nbrOfDays Number of days the session is kept active
     * @return \members\Member User as Member
     */
    public function login(int $memberid, bool $keepLoggedin = true, int $nbrOfDays = 1): \model\members\Member {
        if ($keepLoggedin) {
            setcookie('PHPSESSID', session_id(), time() + (3600 * 24 * $nbrOfDays));
        }
        $_SESSION[APP.'_memberID'] = $memberid;
        $_SESSION['lastActive'] = time();
        $_SESSION['month'] = date_format(new \DateTime(), 'n');
        $_SESSION['year'] = date_format(new \DateTime(), 'Y');
        $_SESSION['origin'] = 'user';
        $_SESSION['searchterm'] = '';
        $_SESSION['rememberMe'] = $keepLoggedin;
        $this->user = User::getInstance($memberid);
//        $this->notifyAuditTrace(__FUNCTION__, func_get_args());
        $this->notifyAuditTrace(__FUNCTION__);
        return $this->user;
    }
    
    /**
     * Stops and removes the SESSION
     */
    public function logout() {
        setcookie('PHPSESSID', '', time());
//        $this->notifyAuditTrace(__FUNCTION__, [$this->user->name .' '. $this->user->lastname]);
    }

    /**
     * Initializes a new password for a Member in the database. The Member will receive a mail confirming his new password.
     * 
     * @param int $memberid Database row id of the Member
     * @param int $pwdlength Length of the generated password
     * @param boolean $requestedByAdmin False if the password requested by the Member self, true if requested by an Administrator
     */
    public function initiatePassword(int $memberid, int $pwdlength = 8, bool $requestedByAdmin = false): void {
 //       $memberMapper = \registry\Registry::instance()->getMemberMapper();
 //       $member = $memberMapper->find($memberid);
 	$member = \model\members\Member::find($memberid);
        $memberName = $member->name;
        $memberLastName = $member->lastname;
        $password = $this->strRand($pwdlength);
        $hash = $this->generateHashPassword($memberid, $password);
        $member->update(array('password' => $hash, 'ownpwd' => '0'));
        $app = APP;
        $subject = $requestedByAdmin ? 
            'Nieuw paswoord '.$app. " (aangevraagd door {$this->user->name} voor {$memberName} {$memberLastName})":
            'Nieuw paswoord '.$app;
        
$body =<<<_MAIL_
Beste $memberName,<br>
<br>       
Uw nieuw paswoord is <b>$password</b><br>
<br>
Dit paswoord kan je slechts 1 maal gebruiken om in te loggen via het login scherm.<br>
<br>
Wanneer je in het scherm <b>Paswoord aanpassen</b> bent, zal je gevraagd worden om een eigen paswoord op te geven.<br>
<br>
Met vriendelijke groet,<br>
Het $app bestuur
_MAIL_;

        $to = $requestedByAdmin ? $this->user->email : $member->email;
        
        \mail\Mailer::sendMail($subject, $body, _MAILTO, $to);
        $fullname = $memberName .' '. $memberLastName;
        $this->notifyAuditTrace(__FUNCTION__, [$fullname]);        
    }
    
    /**
     * Registers the update of a password by a Member in the database
     * 
     * @param \model\Member $user Current Member in the session
     * @param string $password Updated password
     */
    public function changePassword(\model\Member $user, string $password): void {
        $hash = $this->generateHashPassword($user->getId(), $password);
        $reg = \registry\Registry::instance();
        $reg->getMemberMapper()->update($user, array('password' => $hash, 'ownpwd' => '1'));
        
        $this->notifyAuditTrace(__FUNCTION__, [$user->name]);        
    }
    
    /**
     * Generates a random password
     * 
     * @param int $length Length of the random password. Min 1
     * @param string $characters Character set to generate password from
     * @return string or false if the $length is not correct initialized
     */
    private function strRand(int $length = 12,
        string $characters = '0123456789abcdefghijklmnopqrstuvwxyz'): string|false {
        if(!is_int($length) || $length < 0){
            return false;}
        $char_length = strlen($characters) - 1;
        $string = '';

        for($i = $length; $i > 0; $i--){
            $string .= $characters[mt_rand(0, $char_length)];}
        return $string;
    }
    
    /**
     * Algorithm to transform password into a hash
     * 
     * @param int $id Database row id of the Member
     * @param string $password Password as provided by the Member
     * @return string The hash
     */
    private function generateHashPassword(int $id, string $password): string {
        // Create a 256 bit (64 characters) long random salt
        // Let's add 'something random' and the userid
        // to the salt as well for added security
        $salt = hash('sha256', uniqid(mt_rand(), true) . _SALTRAND . strtolower($id));

        // Prefix the password with the salt
        $hash = $salt . $password;

        // Hash the salted password a bunch of times
        for ($i = 0; $i < $this->rand; $i ++) {
            $hash = hash('sha256', $hash);
        }

        // Prefix the hash with the salt so we can find it back later
        $hash = $salt . $hash;
        
        return $hash;
    }
}
