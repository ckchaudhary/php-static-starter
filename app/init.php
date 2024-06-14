<?php

/**
 * The main entry point of the website.
 * All requests go through here.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */


if (defined('SS_PRINT_ERRORS') && SS_PRINT_ERRORS) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'off');
}

// locale, formatting etc
require_once ABSPATH . 'app/inc/core.php';

// current request etc
require_once ABSPATH . 'app/inc/templating.php';

// Project specific functions
require_once ABSPATH . 'public/inc/functions.php';
