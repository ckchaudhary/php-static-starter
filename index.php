<?php

/**
 * The main entry point for public facing site
 *
 * @package PhpSSS
 * @author @ckchaudhary
 * @since 1.0.0
 */

if (file_exists(dirname(__FILE__) . '/config-local.php')) {
    require_once dirname(__FILE__) . '/config-local.php';
} else {
    require_once dirname(__FILE__) . '/config.php';
}

// Load everything!
require_once 'app/init.php';

send_http_headers();
$template_to_load = current_request()->templateToLoad();
include_once $template_to_load;
