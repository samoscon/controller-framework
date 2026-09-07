<?php
/**
 * Mailer.php
 *
 * @package controllerframework\mail
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\mail;

/**
 * Helper class to send application specific mails
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Mailer {
    private static $mailer = null;


    /**
     * Get the Symfony Mailer instance.
     */
    private static function getMailer(): \Symfony\Component\Mailer\Mailer {
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
     * Helper function to send application specific mail. All globals are defined in the config\app_options.ini file
     * 
     * @param string $subject Subject of the mail
     * @param string $body Body of the mail
     * @param string $toBcc Format: mailaddress1<Name1>, mailaddress2<Name2>, mailaddress3<Name3>, etc.
     * @param string $to Format: mailaddress<Name>
     */
    public static function sendMail(string $subject, string $body, string $toBcc, string $to = null): void {
        $mailer = self::getMailer();

        // Create an Email object
        $email = (new \Symfony\Component\Mime\Email());
        
        $email->from(new \Symfony\Component\Mime\Address(_MAILFROM, _MAILFROMNAME));

        $toaddress = $to ? \Symfony\Component\Mime\Address::create($to) : new \Symfony\Component\Mime\Address(_MAILTO, _MAILTONAME);
        $email->to($toaddress);

        $email->replyTo(_MAILREPLYTO);

        $toBccArray = explode(", ", $toBcc, -1);
        foreach ($toBccArray as $bccElement) {
            $bcc = \Symfony\Component\Mime\Address::create($bccElement);
            $email->addBcc($bcc);
        }
        
        $email->subject($subject);
        
        $htmlbody=
            '<html>' .
                '<body>'.'<img src="' . _APPDIR . _LOGO .'" alt="Logo" width="35%" /><br/>'.$body.' </body>'.
            '</html>';       
        $email->html($htmlbody);
                
        // Send the Email
        $mailer->send($email);
    }
}
