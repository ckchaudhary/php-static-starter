<?php

/**
 * Base class for leads.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

namespace RecyleBin\PhpSSS\LMS;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Base class for Leads.
 *
 * @package PhpSSS
 *
 * @author ckchaudhary
 */

abstract class Lead
{
    protected $_type = '';

    protected $_data = [];

    public function __construct($data)
    {
        if (!empty($data)) {
            $this->setData($data);
        }
    }

    public function setData($data)
    {
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $val) {
                $this->_data[$key] = sanitize_text_field($val);
            }
        }
    }

    abstract public function capture();

    protected function sendMail($args)
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $smtp_details = maybe_unserialize(\SMTP_DETAILS);
            //Server settings
            // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                //Send using SMTP
            $mail->Host       = $smtp_details[ 'host' ];    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                       //Enable SMTP authentication
            $mail->Username   = $smtp_details[ 'user' ];    //SMTP username
            $mail->Password   = $smtp_details[ 'password' ];//SMTP password
            if ($smtp_details[ 'port' ] == 587) {
                $mail->Port = 587;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->Port       = 465;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            }

            
            $mail->setFrom($smtp_details[ 'sent_from' ][ 'email' ], $smtp_details[ 'sent_from' ][ 'name' ]);
            $mail->addReplyTo($smtp_details[ 'reply_to' ][ 'email' ], $smtp_details[ 'reply_to' ][ 'name' ]);

            //Recipients
            foreach ($args[ 'to' ] as $to) {
                $name = '';
                $email = $to[ 0 ];
                if (count($to) > 0) {
                    $name = $to[ 1 ];
                }

                $mail->addAddress($email, $name);//Add a recipient
            }

            if (isset($args['cc']) && !empty($args['cc'])) {
                foreach ($args[ 'cc' ] as $to) {
                    $name = '';
                    $email = $to[ 0 ];
                    if (count($to) > 1) {
                        $name = $to[ 1 ];
                    }
    
                    $mail->addCC($email, $name);//Add a recipient
                }
            }

            if (isset($args['bcc']) && !empty($args['bcc'])) {
                foreach ($args[ 'bcc' ] as $to) {
                    $name = '';
                    $email = $to[ 0 ];
                    if (count($to) > 1) {
                        $name = $to[ 1 ];
                    }
    
                    $mail->addBCC($email, $name);//Add a recipient
                }
            }

            if (isset($args['attachments']) && !empty($args['attachments'])) {
                foreach ($args[ 'attachments' ] as $file_path) {
                    $mail->addAttachment($file_path);
                }
            }

            //Content
            $mail->isHTML(true);//Set email format to HTML
            $mail->Subject = $args[ 'subject' ];
            $mail->Body    = $args[ 'body' ];

            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
