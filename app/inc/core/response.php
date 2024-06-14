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
    // @refer https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy
    $content_origins = array(
        "default-src"       => array(
            "'self'",
            "*.google.com",             // for resources from google.com
            "*.google-analytics.com",   // google analytics scripts
        ),

        "font-src"          => array(
            "'self'",
            "fonts.gstatic.com",        // for google fonts
        ),

        "style-src"         => array(
            "'self'",
            "*.googleapis.com",
            "*.jsdelivr.net",
        ),

        "script-src"        => array(
            "'self'",
            "*.google.com",
            "*.googletagmanager.com",
            "*.googleapis.com",
            "*.jsdelivr.net",           // cdn for third party libraries.
            $inline_script_nonce,       // to allow safe inline scripts.
        ),

        "object-src"        => array(
            "'none'",                   // avoid execution of unsafe scripts.
        ),

        "frame-ancestors"   => array(
            "'none'",                   // avoid rendering of page in <frame>, <iframe>, <object>, <embed>, or <applet>
        ),

        "form-action"       => array(
            "'self'",                   // restrict form submission to the self origin.
        ),
    );

    // Use this filter to whitelist other origins or remove some of the defaults.
    $content_origins = \apply_filters('csp_content_origins', $content_origins);

    if (! empty($content_origins)) {
        $temp = array();
        foreach ($content_origins as $k => $v) {
            $temp[] = $k . ' ' . implode(' ', $v) . ';';
        }

        $content_origins = $temp;
    }

    if (\apply_filters('csp_upgrade_insecure_requests', true)) {
        $content_origins[] = "upgrade-insecure-requests;";
    }

    $content_origins = implode(' ', $content_origins);

    // Cache control
    $date_format   = 'D, d M Y H:i:s';
    $expires_header_val = \apply_filters('expires_header_in_seconds', 24 * HOUR_IN_SECONDS);// 24 hours
    $cache_expires_val = \apply_filters('cache_expires_in_seconds', 24 * HOUR_IN_SECONDS);// 24 hours

    // Add desired headers
    $headers = \apply_filters(
        'response__headers_to_add',
        array(
            "Content-Type"              => "text/html; charset=UTF-8",
            "Strict-Transport-Security" => "max-age=31536000",
            "X-Frame-Options"           => "DENY",
            "X-XSS-Protection"          => 0,
            "X-Content-Type-Options"    => "nosniff",
            "Referrer-Policy"           => "strict-origin-when-cross-origin",
            "Content-Security-Policy"   => $content_origins,
            "Expires"                   => gmdate($date_format, time() + $expires_header_val),
            "Cache-Control"             => sprintf("max-age=%d, must-revalidate", $cache_expires_val),
        )
    );

    if (! empty($headers)) {
        foreach ((array) $headers as $name => $field_value) {
            header("{$name}: {$field_value}");
        }
    }


    // Remove unwanted headers
    $headers_to_remove = \apply_filters(
        'response__headers_to_remove',
        array(
            'x-powered-by',
        )
    );
    if (! empty($headers_to_remove)) {
        foreach ($headers_to_remove as $header) {
            header_remove($header);
        }
    }
}
