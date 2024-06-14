<?php

/**
 * http headers, CORS etc.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Because of content security policy, inline scripts are disabled.
 * Enabling those is a security risk.
 * So we use a nonce value, which is mentioned in content security policy.
 * All inline script tags must also use the same nonce to be whitelisted.
 * Use this function wherever required to fetch that nonce.
 *
 * @return string
 */
function inline_script_nonce()
{
    return create_nonce('inline_script_nonce');
}

/**
 * Send http headers.
 * Must be called before any output is sent to buffer.
 *
 * @return void
 */
function send_http_headers()
{
    $inline_script_nonce = "'nonce-" . inline_script_nonce() . "'";
    // Content security policy
    $content_origins = [
    "default-src 'self' *.google.com *.google-analytics.com *.fontawesome.com;", // for resources from google.com
    "font-src 'self' fonts.gstatic.com *.fontawesome.com;",
    "style-src 'self' *.googleapis.com *.jsdelivr.net;",
    "script-src 'self' *.google.com *.googletagmanager.com *.googleapis.com *.jsdelivr.net *.fontawesome.com {$inline_script_nonce};",// cdn for third partly libraries
    "object-src 'none';",// avoid execution of unsafe scripts.
    "frame-ancestors 'none';",//avoid rendering of page in <frame>, <iframe>, <object>, <embed>, or <applet>
    "form-action 'self';",//restrict form submission to the origin which the protected page is being served.
    "upgrade-insecure-requests;",//'upgrade-insecure-requests' and 'block-all-mixed-content' should be set to avoid mixed content (URLs served over HTTP and HTTPS) on the page.
    ];
    $content_origins = implode(' ', $content_origins);

    // Cache control
    $date_format   = 'D, d M Y H:i:s';
    $expires = 24 * 60 * 60;// 24 hours
    $headers['Expires']       = gmdate($date_format, time() + $expires);
    $headers['Cache-Control'] = sprintf(
        'max-age=%d, must-revalidate',
        $expires
    );

    // Remove unwanted headers
    header_remove("server");
    header_remove("x-powered-by");

    // Add desired headers
    $headers = [
    "Content-Type"              => "text/html; charset=UTF-8",
    "Strict-Transport-Security" => "max-age=31536000",
    "X-Frame-Options"           => "DENY",
    "X-XSS-Protection"          => 0,
    "X-Content-Type-Options"    => "nosniff",
    "Referrer-Policy"           => "strict-origin-when-cross-origin",
    "Content-Security-Policy"   => $content_origins,
    "Expires"                   => gmdate($date_format, time() + $expires),
    "Cache-Control"             => sprintf("max-age=%d, must-revalidate", $expires),
    ];
    foreach ((array) $headers as $name => $field_value) {
        header("{$name}: {$field_value}");
    }
}
