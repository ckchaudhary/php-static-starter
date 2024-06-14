<?php

/**
 * Determine which template file to load, functions to generate pagination, navs etc.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Pagination stuff.
require_once ABSPATH . 'app/inc/templating/functions-pagination.php';
// The singleton object that has details of current http request.
require_once ABSPATH . 'app/inc/templating/class-request.php';
// body_class, site_title, header footer etc.
require_once ABSPATH . 'app/inc/templating/template-tags.php';
// Functions to print menu.
require_once ABSPATH . 'app/inc/templating/navigation.php';

/**
 * Returns the (singleton) instance of Request class
 *
 * @return \RecyleBin\PhpSSS\Request
 */
function current_request()
{
    return \RecyleBin\PhpSSS\Request::instance();
}

current_request()->parse();
