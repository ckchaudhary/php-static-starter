<?php

/**
 * The main Configuration file.
 * Define all your constants here.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// root directory
define('ABSPATH', __DIR__ . '/');

// If this website's address is yourdomain.com then the SUBDIRECTORY should be empty.
// If this website's address is yourdomain.com/myapp then the SUBDIRECTORY should be 'myapp'
define('SUBDIRECTORY', 'php-static-starter');

// Your website's root url.
define('HOME_URL', 'http://localhost/php-static-starter/');
define('ASSETS_URL', 'http://localhost/php-static-starter/public/assets/');

// Sub directory for uploads folder, relative to root folder of the domain, in which your website resides.
// All system generated ( e.g: log files ) and user uploads should go into this folder, or one its sub-folders.
define('UPLOADS_DIR', 'uploads');

// time zone, in which you want to display date and time by default.
define('TIME_ZONE', 'Asia/Kolkata');
// default date and time formats
define('DATE_FORMAT', 'd M Y');
define('TIME_FORMAT', 'H:i:s');

// go to https://www.recycleb.in/tools/keygen/ and copy the generated key from there.
define('PUBLIC_ENCRYPTION_SECRET', '');

/**
 * Optional: google recaptcha keys.
 *
 * This is needed only if you plan on using google recaptcha( e.g: in a contact form ).
 */
define('GOOGLE_RECAPTCHA_V2_SITE_KEY', '');
define('GOOGLE_RECAPTCHA_V2_SECRET_KEY', '');

/**
 * Error reporting.
 *
 * Set to true to print errors.
 *
 * !important: This must be set to false in production.
 */
define('SS_PRINT_ERRORS', false);

/**
 * Debug mode.
 *
 * When set to true it enables features like logging.
 *
 * !important: This should be set to false in production.
 */
define('SS_DEBUG', false);

/**
 * SMTP Details.
 *
 * phpmailer is used when sending emails.( e.g: from a contact form ).
 * php's default mail function is unreliable.
 * we use SMTP to send emails.
 *
 * Enter the smtp details of the email account you wish to use to send emails.
 * You can get all these details from your smtp provider
 */
define('SMTP_DETAILS', serialize([
    'host' => '',
    'port' => '465',// Accepted values 465 or 587
    'user' => '',
    'password' => '',

    'sent_from' => [
        'email' => '',
        'name' => '',
    ],

    'reply_to' => [
        'email' => '',
        'name' => '',
    ],
]));

/**
 * By default all the emails( from contact forms for example ) are sent to the email addresses defined below.
 *
 * You can
 */
define('LMS_EMAIL_CONTACTS', serialize([
    'to' => [
        // e.g:
        // [ 'john.doe@example.com', 'John Doe' ],
    ],
    'cc' => [
        // a list of recipients in the following format
        // [ 'someone@example.com', 'Some One' ],
        // [ 'someoneelse@example.com', 'Some One Else' ],
    ],
    'bcc' => [
        // a list of recipients in the following format
        // [ 'someone@example.com', 'Some One' ],
        // [ 'someoneelse@example.com', 'Some One Else' ],
    ],
]));
