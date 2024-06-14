<?php

/**
 * Leads!
 * Capturing contact form data and other associated/similar stuff.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Include different lead types
require_once ABSPATH . 'app/inc/lms/class-lead.php';
require_once ABSPATH . 'app/inc/lms/class-contact-form.php';
require_once ABSPATH . 'app/inc/lms/class-newsletter-subscription.php';

// Include phpmailer
require_once ABSPATH . 'app/lib/PHPMailer-master/src/Exception.php';
require_once ABSPATH . 'app/lib/PHPMailer-master/src/PHPMailer.php';
require_once ABSPATH . 'app/lib/PHPMailer-master/src/SMTP.php';
