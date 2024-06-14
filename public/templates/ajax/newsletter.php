<?php

/**
 * The newsletter form submission handler.
 *
 * @package PhpSSS
 * @subpackage forms
 * @author  @ckchaudhary
 * @since   1.0.0
 */

header('Content-Type: application/json; charset=utf-8');

// the key 'newsletter-subscription' here must be the same used when generating nonce.
// Refer: public/templates/parts/newsletter-form.php
if (!verify_nonce(@$_POST[ '_nonce' ], 'newsletter-subscription')) {
    http_response_code(401);
    die(json_encode([ 'status' => 'error', 'data' => [ 'message' => 'Security token has expired. Please reload the page and try again.' ] ]));
}

$is_valid = true;
$required_fields = [ 'email' ];
foreach ($required_fields as $field) {
    if (! isset($_POST[ $field ]) || empty(trim($_POST[ $field ]))) {
        $is_valid = false;
        break;
    }
}

if (! $is_valid) {
    http_response_code(400);
    die(json_encode([ 'status' => 'error', 'data' => [ 'message' => 'Please fill all required fields first.' ] ]));
}

// verify email
$email = trim($_POST[ 'email' ]);
if (! is_email($email)) {
    http_response_code(400);
    die(json_encode([ 'status' => 'error', 'data' => [ 'message' => 'Please enter a valid email address.' ] ]));
}

// Everything looks good
require_once ABSPATH . 'app/inc/lms.php';
$data = [];
$all_fields = [ 'email' ];
foreach ($all_fields as $field) {
    $data[$field] = sanitize_text_field($_POST[$field]);
}
$lead = new \RecyleBin\PhpSSS\LMS\NewsletterSubscription($data);
$lead->capture();

http_response_code(200);
die(json_encode([ 'status' => 'success', 'data' => [ 'message' => 'Subscription successful!' ] ]));
