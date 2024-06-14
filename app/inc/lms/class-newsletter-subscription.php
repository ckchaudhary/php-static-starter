<?php

/**
 * Base class for leads.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

namespace RecyleBin\PhpSSS\LMS;

/**
 * Capture newsletter subscriptions.
 */
class NewsletterSubscription extends Lead
{
    /**
     * Constructor
     *
     * @param mixed $data initial options.
     */
    public function __construct($data = '')
    {
        $this->_type = 'newsletter';
        parent::__construct($data);
    }

    public function capture()
    {
        if (empty($this->_data)) {
            return false;
        }

        // The captured email can be accessed using $this->_data['email] .
        \RecyleBin\PhpSSS\DebugLog::log('newsletter subscription from ' . $this->_data['email'], 'newsletter');

        // Add code here to send the email to a third party service like CRM.

        return true;
    }
}
