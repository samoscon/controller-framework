<?php
/**
 * MailQueue.php
 *
 * @package controllerframework\mail
 * @version 1.0
 * @copyright (c) 2026, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */

namespace controllerframework\mail;

/**
 * Description of MailQueue: Asynchron queue to send out bulk mails. This class helps to store new mails to the table mail_queue
 *
 * @author dirk
 */
class MailQueue {
    /**
     * Database connection.
     *
     */
    private static function getConnection()
    {
        return new \PDO(
            'mysql:host=' . _DBHOST . ';dbname=' . _DBNAME . ';charset=utf8mb4',
            _DBUSER,
            _DBPASSWORD,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }


    /**
     * Add one mail to the queue.
     */
    public static function add(
        string $subject,
        string $body,
        string $recipient,
        string $bcc = ''
    ): int {

        $pdo = self::getConnection();

        $sql = "
            INSERT INTO mail_queue
                (subject, body, recipient, bcc)
            VALUES
                (:subject, :body, :recipient, :bcc)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':subject'   => $subject,
            ':body'      => $body,
            ':recipient' => $recipient,
            ':bcc'       => $bcc ?: null
        ]);

        return (int) $pdo->lastInsertId();
    }
}
