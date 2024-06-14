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

/**
 * create nonce for given phrase.
 *
 * @param  string $action
 * @return string
 */
function create_nonce($action = -1)
{
    $i = nonce_tick();
    return substr(wp_hash($i . '|' . $action, 'nonce'), -12, 10);
}

/**
 * Verify the nonce against the source phrase.
 *
 * @param  string $nonce
 * @param  string $action
 * @return boolean
 */
function verify_nonce($nonce, $action = -1)
{
    $nonce = (string) $nonce;
    if (empty($nonce)) {
        return false;
    }

    $i = nonce_tick();

    // Nonce generated 0-12 hours ago.
    $expected = substr(wp_hash($i . '|' . $action, 'nonce'), -12, 10);
    if (hash_equals($expected, $nonce)) {
        return 1;
    }

    // Nonce generated 12-24 hours ago.
    $expected = substr(wp_hash(( $i - 1 ) . '|' . $action, 'nonce'), -12, 10);
    if (hash_equals($expected, $nonce)) {
        return 2;
    }

    // Invalid nonce.
    return false;
}

function nonce_tick()
{
    $nonce_life = DAY_IN_SECONDS;
    return ceil(time() / ( $nonce_life / 2 ));
}

function wp_hash($data)
{
    return hash_hmac('md5', $data, PUBLIC_ENCRYPTION_SECRET);
}
