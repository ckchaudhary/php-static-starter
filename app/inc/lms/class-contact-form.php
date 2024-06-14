<?php

/**
 * Lead from the contact form.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

namespace RecyleBin\PhpSSS\LMS;

/**
 * Capture leads from a contact form.
 */
class ContactForm extends Lead
{
    /**
     * Constructor
     *
     * @param mixed $data initial options.
     */
    public function __construct($data = '')
    {
        $this->_type = 'contact-form';
        parent::__construct($data);
    }

    public function capture()
    {
        if (empty($this->_data)) {
            return false;
        }

        $receivers = maybe_unserialize(\LMS_EMAIL_CONTACTS);

        $email_args = [
        'subject'     => 'New lead from contact form',
        'body'         => $this->generateEmailBody(),
        'to'         => $receivers[ 'to' ],
        ];
        if (isset($receivers[ 'cc' ]) && !empty($receivers[ 'cc' ])) {
            $email_args[ 'cc' ] = $receivers[ 'cc' ];
        }
        if (isset($receivers[ 'bcc' ]) && !empty($receivers[ 'bcc' ])) {
            $email_args[ 'bcc' ] = $receivers[ 'bcc' ];
        }

        // before uncommenting the code below ensure that all the required information( SMTP_DETAILS, LMS_EMAIL_CONTACTS ) is furnished in config.php
        // $this->sendMail($email_args);
        
        $log_message = 'New submission on contact form' . PHP_EOL;
        $log_message .= "Name: " . stripslashes($this->_data[ 'y-name' ]) . PHP_EOL;
        $log_message .= "Email: " . stripslashes($this->_data[ 'y-email' ]) . PHP_EOL;
        $log_message .= "Message: " . stripslashes($this->_data[ 'y-msg' ]) . PHP_EOL;
        \RecyleBin\PhpSSS\DebugLog::log($log_message);
        
        return true;
    }

    protected function generateEmailBody()
    {
        $html = "The information provided by lead is:<br>";

        $html .= "<strong>Name:</strong><br>";
        $html .= stripslashes($this->_data[ 'y-name' ]);
        $html .= "<p></p>";

        $html .= "<strong>Email:</strong><br>";
        $html .= stripslashes($this->_data[ 'y-email' ]);
        $html .= "<p></p>";

        $html .= "<strong>Message:</strong><br>";
        $html .= stripslashes($this->_data[ 'y-msg' ]);
        $html .= "<p></p>";

        return $html;
    }
}
