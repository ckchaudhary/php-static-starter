<?php

/**
 * Load all the background stuff.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// set the default date time zone, etc.
require_once ABSPATH . 'app/inc/core/locale.php';

// Formatting and sanitization helper functions.
require_once ABSPATH . 'app/inc/core/formatting.php';

// query builder functions.
require_once ABSPATH . 'app/inc/core/query.php';

// WordPress' hooks system
require_once ABSPATH . 'app/inc/core/class-wp-hook.php';
require_once ABSPATH . 'app/inc/core/plugin.php';

// Nonce functions.
require_once ABSPATH . 'app/inc/core/authentication.php';

// Http headers etc.
require_once ABSPATH . 'app/inc/core/response.php';

// Http headers etc.
require_once ABSPATH . 'app/inc/core/class-debug-log.php';
