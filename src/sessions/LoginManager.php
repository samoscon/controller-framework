<?php
/**
 * LoginManager.php
 *
 * @package controllerframework\sessions
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\sessions;

use controllerframework\registry\Registry;
use controllerframework\audit\{AuditableItem, AuditableItemTrait};

use controllerframework\members\Member;

/**
 * Manages the login (including management of usernames and passwords) and the logout of a User in a session. 
 * Each password change and login will be notified to an audit trace.
 * 
 * @link ../graphs/sessions%20Class%20Diagram.svg Sessions class diagram
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class LoginManager implements AuditableItem {
    use AuditableItemTrait;
    
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
     * @var User Member in the session 
     */
    protected ?Member $user = null;

    private const REMEMBER_COOKIE = 'controllerframework_remember';
    private const REMEMBER_DAYS = 30;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Registry::instance()->getDb();
        $this->lastActive = 15;

        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }

        if (isset($_SESSION[APP.'_memberID'])) {
            $memberid = $_SESSION[APP.'_memberID'];
            $this->user = User::getInstance($memberid);
        } else {
            $this->loginFromRememberToken();
        }
    }
    
    /**
     * Returns User of the session as a Member object
     * 
     * @return User or Null
     */
    public function getUser(): ?Member {
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
     * Checks the password of the Member during login.
     *
     * Existing legacy SHA-256 password hashes are automatically
     * migrated to password_hash() after a successful login.
     *
     * @param int $memberid Database row id of the Member
     * @param string $password Password as provided by the Member
     * @return bool
     */
    public function validatePassword(
        int $memberid,
        string $password
    ): bool {

        $sql = $this->db->prepare(
            "SELECT password
             FROM member
             WHERE id = ?
             LIMIT 1"
        );

        $sql->execute([$memberid]);
        $row = $sql->fetch();
        $sql->closeCursor();

        if (!$row || empty($row['password'])) {
            return false;
        }

        $storedHash = $row['password'];

        /*
         * First try the new password_hash() format.
         */
        if (password_verify($password, $storedHash)) {

            /*
             * Rehash automatically if PHP recommends a newer
             * password hashing configuration.
             */
            if (password_needs_rehash(
                $storedHash,
                PASSWORD_DEFAULT
            )) {
                $newHash = $this->generateHashPassword($password);

                $update = $this->db->prepare(
                    "UPDATE member
                     SET password = ?
                     WHERE id = ?"
                );

                $update->execute([
                    $newHash,
                    $memberid
                ]);
            }

            return true;
        }

        /*
         * If the new hash didn't work, try the legacy SHA-256
         * password format.
         */
        if ($this->isLegacyPasswordHash($storedHash)) {
            if ($this->validateLegacyPassword(
                $password,
                $storedHash
            )) {

            /*
             * Successful legacy login:
             * immediately replace the old hash by a modern one.
             */
                $newHash = $this->generateHashPassword($password);

                $update = $this->db->prepare(
                    "UPDATE member
                     SET password = ?
                     WHERE id = ?"
                );

                $update->execute([
                    $newHash,
                    $memberid
                ]);

                return true;
            }
        }

        return false;
    }

    /**
     * Creates SESSION after a successful login 
     * 
     * @param int $memberid Database row id of the Member
     * @param boolean $keepLoggedin Default =  true
     * @return \members\Member User as Member
     */
    public function login(int $memberid, bool $keepLoggedin = true): Member {

        // Prevent session fixation after successful authentication.
        session_regenerate_id(true);

        $_SESSION[APP.'_memberID'] = $memberid;
        $_SESSION['lastActive'] = time();
        $_SESSION['month'] = date_format(new \DateTime(), 'n');
        $_SESSION['year'] = date_format(new \DateTime(), 'Y');
        $_SESSION['origin'] = 'user';
        $_SESSION['searchterm'] = '';
        $_SESSION['rememberMe'] = $keepLoggedin;
        $this->user = User::getInstance($memberid);

        if ($keepLoggedin) {
            $this->createRememberToken($memberid);
        } else {
            // Zorg ervoor dat een eventueel oud remember-token
            // niet blijft bestaan.
            $this->deleteRememberTokens($memberid);
            $this->deleteRememberCookie();
        }
        
//        $this->notifyAuditTrace(__FUNCTION__, func_get_args());
        $this->notifyAuditTrace(__FUNCTION__);
        
        return $this->user;
    }
    
    /**
     * Stops and removes the SESSION
     */
    public function logout(): void {
        $memberid = isset($_SESSION[APP.'_memberID'])
            ? (int) $_SESSION[APP.'_memberID']
            : null;

        /*
         * Verwijder alle remember-me tokens van deze gebruiker.
         */
        if ($memberid !== null) {
            $this->deleteRememberTokens($memberid);
        }

        /*
         * Verwijder de remember-me cookie.
         */
        $this->deleteRememberCookie();
        
        // Remove all session variables.
        $_SESSION = [];

        // Remove the session cookie.
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                [
                    'expires'  => time() - 42000,
                    'path'     => $params['path'],
                    'domain'   => $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Lax'
                ]
            );
        }

        // Destroy the server-side session.
        session_destroy();

        // Remove the current user from this LoginManager instance.
        $this->user = null;
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
        $member = \model\Member::find($memberid);

        $password = $this->strRand($pwdlength);
        $hash = $this->generateHashPassword($password);

        $member->update([
            'password' => $hash,
            'ownpwd' => '0'
        ]);

        $app = APP;
        $subject = 'Password ' . $app;
        $body = $member->initiatePassword($password);
        $to = $requestedByAdmin
            ? $this->user->email
            : $member->email;

        \controllerframework\mail\Mailer::sendMail(
            $subject,
            $body,
            _MAILTO,
            $to
        );
    }
    
    /**
     * Registers the update of a password by a Member in the database
     * 
     * @param Member $user Current Member in the session
     * @param string $password Updated password
     */
    public function changePassword(Member $user, string $password): void {
        $hash = $this->generateHashPassword($password);

        $user->update([
            'password' => $hash,
            'ownpwd' => '1'
        ]);

        $this->notifyAuditTrace(
            __FUNCTION__,
            [$user->name]
        );
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
        if ($length < 1 || $characters === '') {
            return false;
        }

        $charLength = strlen($characters);
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[
                random_int(0, $charLength - 1)
            ];
        }

        return $string;
    }
    
    /**
     * Generates a secure password hash.
     *
     * @param string $password Password as provided by the Member
     * @return string The password hash
     */
    private function generateHashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Validates whether a hash is an old SHA-256 password hash.
     *
     * This method is only used during migration to the new
     * password_hash/password_verify mechanism.
     *
     * @param string $hash Existing legacy hash
     * @return bool
     */
    private function isLegacyPasswordHash(string $hash): bool
    {
        return strlen($hash) === 128
            && ctype_xdigit($hash);
    }

    /**
     * Validates a password against the old SHA-256 password hash.
     *
     * This method is only used during migration to the new
     * password_hash/password_verify mechanism.
     *
     * @param string $password Password provided by the Member
     * @param string $storedHash Existing legacy hash
     * @return bool
     */
    private function validateLegacyPassword(
        string $password,
        string $storedHash
    ): bool {

        $salt = substr($storedHash, 0, 64);
        $hash = $salt . $password;

        for ($i = 0; $i < _RAND; $i++) {
            $hash = hash('sha256', $hash);
        }

        $hash = $salt . $hash;

        return hash_equals($storedHash, $hash);
    }

    /**
     * Creates a record in the table remember_tokens
     *
     * @param int $memberid 1 token per member
     */
    private function createRememberToken(int $memberid): void
    {
        // Verwijder eventueel bestaande tokens van deze gebruiker.
        // Hierdoor blijft er maximaal één actieve remember-me login per gebruiker.
        $this->deleteRememberTokens($memberid);

        // Selector is niet geheim.
        $selector = bin2hex(random_bytes(12));

        // Validator is het geheime gedeelte.
        $validator = bin2hex(random_bytes(32));

        // Alleen de hash van de validator komt in de database.
        $tokenHash = hash('sha256', $validator);

        $expiresAt = new \DateTimeImmutable(
            '+' . self::REMEMBER_DAYS . ' days'
        );

        $sql = $this->db->prepare(
            "INSERT INTO remember_tokens
                (member_id, selector, token_hash, expires_at)
             VALUES
                (?, ?, ?, ?)"
        );

        $sql->execute([
            $memberid,
            $selector,
            $tokenHash,
            $expiresAt->format('Y-m-d H:i:s')
        ]);

        // Cookie bevat alleen selector + validator.
        $cookieValue = $selector . '.' . $validator;

        setcookie(
            self::REMEMBER_COOKIE,
            $cookieValue,
            [
                'expires' => $expiresAt->getTimestamp(),
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }
    
    /**
     * Methode die automatisch wordt uitgevoerd wanneer iemand terugkomt nadat de browser gesloten is
     *
     * @return bool true/false
     */
    private function loginFromRememberToken(): bool
    {
        if (empty($_COOKIE[self::REMEMBER_COOKIE])) {
            return false;
        }

        $cookie = $_COOKIE[self::REMEMBER_COOKIE];

        // Cookie moet exact uit selector.validator bestaan.
        $parts = explode('.', $cookie, 2);

        if (count($parts) !== 2) {
            $this->deleteRememberCookie();
            return false;
        }

        [$selector, $validator] = $parts;

        // Controleer formaat.
        if (
            !preg_match('/^[a-f0-9]{24}$/', $selector) ||
            !preg_match('/^[a-f0-9]{64}$/', $validator)
        ) {
            $this->deleteRememberCookie();
            return false;
        }

        $sql = $this->db->prepare(
            "SELECT id, member_id, token_hash, expires_at
             FROM remember_tokens
             WHERE selector = ?
             LIMIT 1"
        );

        $sql->execute([$selector]);

        $row = $sql->fetch(\PDO::FETCH_ASSOC);
        $sql->closeCursor();

        if (!$row) {
            $this->deleteRememberCookie();
            return false;
        }

        // Token is verlopen.
        if (strtotime($row['expires_at']) < time()) {
            $this->deleteRememberTokenById((int) $row['id']);
            $this->deleteRememberCookie();
            return false;
        }

        // Vergelijk de hash constant-time.
        $tokenHash = hash('sha256', $validator);

        if (!hash_equals($row['token_hash'], $tokenHash)) {
            $this->deleteRememberCookie();
            return false;
        }

        $memberid = (int) $row['member_id'];

        // Controleer opnieuw of de gebruiker nog actief is.
        $sql = $this->db->prepare(
            "SELECT id
             FROM member
             WHERE id = ?
               AND active = '1'
             LIMIT 1"
        );

        $sql->execute([$memberid]);

        $member = $sql->fetch(\PDO::FETCH_ASSOC);
        $sql->closeCursor();

        if (!$member) {
            $this->deleteRememberTokenById((int) $row['id']);
            $this->deleteRememberCookie();
            return false;
        }

        /*
         * De remember-token is geldig.
         *
         * We maken nu een nieuwe PHP-session ID.
         */
        session_regenerate_id(true);

        $_SESSION[APP.'_memberID'] = $memberid;
        $_SESSION['lastActive'] = time();
        $_SESSION['month'] = date('n');
        $_SESSION['year'] = date('Y');
        $_SESSION['origin'] = 'remember';
        $_SESSION['searchterm'] = '';
        $_SESSION['rememberMe'] = true;

        $this->user = User::getInstance($memberid);

        /*
         * Token wordt na gebruik vervangen.
         *
         * Daardoor kan hetzelfde remember-token niet onbeperkt
         * opnieuw gebruikt worden.
         */
        $this->createRememberToken($memberid);

        return true;
    }

    /**
     * Deletes a record in the table remember_tokens
     *
     * @param int $memberid 1 token per member
     */
    private function deleteRememberTokens(int $memberid): void
    {
        $sql = $this->db->prepare(
            "DELETE FROM remember_tokens
             WHERE member_id = ?"
        );

        $sql->execute([$memberid]);
        $sql->closeCursor();
    }

    /**
     * Deletes a record in the table remember_tokens
     *
     * @param int $id id of the record in the database
     */
    private function deleteRememberTokenById(int $id): void
    {
        $sql = $this->db->prepare(
            "DELETE FROM remember_tokens
             WHERE id = ?"
        );

        $sql->execute([$id]);
        $sql->closeCursor();
    }

    /**
     * Deletes the cookie
     *
     */
    private function deleteRememberCookie(): void
    {
        setcookie(
            self::REMEMBER_COOKIE,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        unset($_COOKIE[self::REMEMBER_COOKIE]);
    }    
    
}
