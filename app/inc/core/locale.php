<?php

/**
 * Create and verify nonce etc.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Set default date time zone
date_default_timezone_set(\TIME_ZONE);

/**
 * Get the timezone.
 *
 * @return \datetimezone
 */
function wp_timezone()
{
    return new datetimezone(\TIME_ZONE);
}
