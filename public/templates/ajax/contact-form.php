<?php

/**
 * The contact form submission handler.
 *
 * @package PhpSSS
 * @subpackage forms
 * @author  @ckchaudhary
 * @since   1.0.0
 */

header('Content-Type: application/json; charset=utf-8');

if (!verify_nonce(@$_POST[ '_nonce' ], 'contact-form')) {
    http_response_code(401);
    die(json_encode([ 'status' => 'error', 'data' => [ 'message' => 'Security token has expired. Please reload the page and try again.' ] ]));
}

$is_valid = true;
$required_fields = [ 'y-email', 'y-msg' ];
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
$email = trim($_POST[ 'y-email' ]);
if (! is_email($email)) {
    http_response_code(400);
    die(json_encode([ 'status' => 'error', 'data' => [ 'message' => 'Please enter a valid email address.' ] ]));
}

// Verify captcha
if (defined('GOOGLE_RECAPTCHA_V2_SECRET_KEY') && !empty('GOOGLE_RECAPTCHA_V2_SECRET_KEY') && isset($_POST[ 'g-recaptcha-response' ])) {
    $captcha = $_POST[ 'g-recaptcha-response' ];
    $ip = $_SERVER['REMOTE_ADDR'];
    // Post request to server
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode(GOOGLE_RECAPTCHA_V2_SECRET_KEY) . '&response=' . urlencode($captcha);
    $response = file_get_contents($url);
    $responseKeys = json_decode($response, true);
    // Should return JSON with success as true
    if (! $responseKeys[ "success" ]) {
        http_response_code(400);
        die(json_encode([ 'status' => 'error', 'data' => [ 'message' => 'Please complete the captcha again.' ] ]));
    }
}

// Everything looks good
include_once ABSPATH . 'app/inc/lms.php';
$data = [];
$text_fields = [ 'y-email', 'y-name' ];
foreach ($text_fields as $field) {
    if (!isset($_POST[ $field ]) || empty($_POST[$field])) {
        continue;
    }
    $data[$field] = sanitize_text_field($_POST[$field]);
}
$textarea_fields = [ 'y-msg' ];
foreach ($textarea_fields as $field) {
    if (!isset($_POST[ $field ]) || empty($_POST[$field])) {
        continue;
    }
    $data[$field] = sanitize_textarea_field($_POST[$field]);
}

$lead = new \RecyleBin\PhpSSS\LMS\ContactForm($data);
$lead->capture();

http_response_code(200);
die(json_encode([ 'status' => 'success', 'data' => [ 'message' => 'Thanks for reaching out. We\'ll get back ASAP.' ] ]));
