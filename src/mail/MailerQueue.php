<?php
/**
 * MailerQueue.php
 *
 * @package controllerframework\mail
 * @version 1.0
 * @copyright (c) 2026, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */

namespace controllerframework\mail;

/**
 * Helper class to send bulk mails from the mail-queue in the database to the SMTP server
 */
class MailerQueue
{
    private static $mailer = null;


    /**
     * Get the Symfony Mailer instance.
     */
    private static function getMailer()
    {
        if (self::$mailer === null) {
            $dsn =
                'smtp://' .
                rawurlencode(_MAILUSERNAME) . ':' .
                rawurlencode(_MAILPASSWORD) . '@' .
                _MAILHOST . ':' .
                _MAILHOSTPORT;

            $transport =
                \Symfony\Component\Mailer\Transport::fromDsn($dsn);

            self::$mailer =
                new \Symfony\Component\Mailer\Mailer($transport);
        }

        return self::$mailer;
    }


    /**
     * Send an application specific mail.
     *
     * @param string $subject
     * @param string $body
     * @param string $toBcc
     * @param string|null $to
     */
    public static function sendMail(
        $subject,
        $body,
        $toBcc,
        $to = null
    ) {

        $mailer = self::getMailer();

        $email = new \Symfony\Component\Mime\Email();

        $email->from(
            new \Symfony\Component\Mime\Address(
                _MAILFROM,
                _MAILFROMNAME
            )
        );

        $toAddress = $to
            ? new \Symfony\Component\Mime\Address(
                $to,
                _MAILFROMNAME
            )
            : new \Symfony\Component\Mime\Address(
                _MAILTO,
                _MAILFROMNAME
            );

        $email->to($toAddress);

        $email->replyTo(_MAILREPLYTO);


        /*
         * BCC
         */
        if ($toBcc) {

            $toBccArray = array_filter(
                array_map(
                    'trim',
                    explode(',', $toBcc)
                )
            );

            foreach ($toBccArray as $bcc) {

                if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                    $email->addBcc($bcc);
                }
            }
        }


        $email->subject($subject);


        $htmlbody =
            '<html>' .
            '<body>' .
            '<img src="' . _APPDIR .
            'assets/'.APP.'.jpg" alt="Image" /><br/>' .
            $body .
            '</body>' .
            '</html>';

        $email->html($htmlbody);


        /*
         * Send
         */
        $mailer->send($email);
    }
}