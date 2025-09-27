<?php
/**
 * Mailer.php
 *
 * @package mail
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace mail;

/**
 * Helper class to send application specific mails
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Mailer {
    
    /**
     * Helper function to send application specific mail. All globals are defined in the config\app_options.ini file
     * 
     * @param string $subject Subject of the mail
     * @param string $body Body of the mail
     * @param string $toBcc Format: mailaddress1<Name1>, mailaddress2<Name2>, mailaddress3<Name3>, etc.
     * @param string $to Format: mailaddress<Name>
     */
    public static function sendMail(string $subject, string $body, string $toBcc, string $to = null): void {
        // Symfony Mailer Library
        require_once './vendor/autoload.php';
        
        // Mail Transport
        
        // Create a Transport object
        $transport = \Symfony\Component\Mailer\Transport::fromDsn('smtp://'._MAILUSERNAME.':'._MAILPASSWORD.'@'._MAILHOST.':'._MAILHOSTPORT);

        // Create a Mailer object
        $mailer = new \Symfony\Component\Mailer\Mailer($transport); 

        // Create an Email object
        $email = (new \Symfony\Component\Mime\Email());
        
        $email->from(new \Symfony\Component\Mime\Address(_MAILFROM, _MAILFROMNAME));

        $toaddress = $to ? \Symfony\Component\Mime\Address::create($to) : new \Symfony\Component\Mime\Address(_MAILTO, _MAILTONAME);
        $email->to($toaddress);

        $email->replyTo(_MAILREPLYTO);

        $toBccArray = explode(", ", $toBcc, -1);
        foreach ($toBccArray as $bcc) {
            $email->addBcc($bcc);
        }
        
        $email->subject($subject);
        
        $htmlbody=
            '<html>' .
                '<body>'.'<img src="'. _LOGO.'" alt="Logo" width="35%" /><br/>'.$body.' </body>'.
            '</html>';       
        $email->html($htmlbody);
                
        // Send the Email
        $mailer->send($email);
    }
}
