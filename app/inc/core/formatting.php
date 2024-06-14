<?php

/**
 * Various sanitization, validation and formatting functions.
 * Blatantly copied from WordPress core. No qualms about it.
 *
 * @package PhpSSS
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

define('SITE_CHARSET', 'UTF-8');
define('HOUR_IN_SECONDS', 60 * 60);
define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);

/**
 * Sanitizes a string key.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters,
 * dashes, and underscores are allowed.
 *
 * @since 1.0.0
 *
 * @param string $key String key.
 *
 * @return string Sanitized key.
 */
function sanitize_key($key)
{
    $sanitized_key = '';

    if (is_scalar($key)) {
        $sanitized_key = strtolower($key);
        $sanitized_key = preg_replace('/[^a-z0-9_\-]/', '', $sanitized_key);
    }

    return $sanitized_key;
}

/**
 * Sanitizes a string into a slug, which can be used in URLs or HTML attributes.
 *
 * By default, converts accent characters to ASCII characters and further
 * limits the output to alphanumeric characters, underscore (_) and dash (-)
 * through the {@see 'sanitize_title'} filter.
 *
 * If `$title` is empty and `$fallback_title` is set, the latter will be used.
 *
 * @since 1.0.0
 *
 * @param  string $title          The string to be sanitized.
 * @param  string $fallback_title Optional. A title to use if $title is empty. Default empty.
 * @param  string $context        Optional. The operation for which the string is sanitized.
 *                                When set to 'save', the string runs through remove_accents().
 *                                Default 'save'.
 * @return string The sanitized string.
 */
function slugify($title, $fallback_title = '')
{
    $slug = \Transliterator::createFromRules(
        ':: Any-Latin;'
        . ':: NFD;'
        . ':: [:Nonspacing Mark:] Remove;'
        . ':: NFC;'
        . ':: [:Punctuation:] Remove;'
        . ':: Lower();'
        . '[:Separator:] > \'-\''
    )->transliterate($title);

    if ('' === $slug || false === $slug) {
        $slug = $fallback_title;
    }

    return $slug;
}

/**
 * Sanitizes an HTML classname to ensure it only contains valid characters.
 *
 * Strips the string down to A-Z,a-z,0-9,_,-. If this results in an empty
 * string then it will return the alternative value supplied.
 *
 * @since 1.0.0
 *
 * @param  string $class    The classname to be sanitized
 * @param  string $fallback Optional. The value to return if the sanitization ends up as an empty string.
 *                          Defaults to an empty string.
 * @return string The sanitized value
 */
function sanitize_html_class($class, $fallback = '')
{
    // Strip out any %-encoded octets.
    $sanitized = preg_replace('|%[a-fA-F0-9][a-fA-F0-9]|', '', $class);

    // Limit to A-Z, a-z, 0-9, '_', '-'.
    $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $sanitized);

    if ('' === $sanitized && $fallback) {
        return sanitize_html_class($fallback);
    }
    
    return $sanitized;
}

/**
 * Converts lone & characters into `&#038;` (a.k.a. `&amp;`)
 *
 * @since 1.0.0
 *
 * @param  string $content String of characters to be converted.
 * @return string Converted string.
 */
function convert_chars($content)
{
    if (strpos($content, '&') !== false) {
        $content = preg_replace('/&([^#])(?![a-z1-4]{1,8};)/i', '&#038;$1', $content);
    }

    return $content;
}

/**
 * Converts invalid Unicode references range to valid range.
 *
 * @since 1.0.0
 *
 * @param  string $content String with entities that need converting.
 * @return string Converted string.
 */
function convert_invalid_entities($content)
{
    $wp_htmltranswinuni = array(
    '&#128;' => '&#8364;', // The Euro sign.
    '&#129;' => '',
    '&#130;' => '&#8218;', // These are Windows CP1252 specific characters.
    '&#131;' => '&#402;',  // They would look weird on non-Windows browsers.
    '&#132;' => '&#8222;',
    '&#133;' => '&#8230;',
    '&#134;' => '&#8224;',
    '&#135;' => '&#8225;',
    '&#136;' => '&#710;',
    '&#137;' => '&#8240;',
    '&#138;' => '&#352;',
    '&#139;' => '&#8249;',
    '&#140;' => '&#338;',
    '&#141;' => '',
    '&#142;' => '&#381;',
    '&#143;' => '',
    '&#144;' => '',
    '&#145;' => '&#8216;',
    '&#146;' => '&#8217;',
    '&#147;' => '&#8220;',
    '&#148;' => '&#8221;',
    '&#149;' => '&#8226;',
    '&#150;' => '&#8211;',
    '&#151;' => '&#8212;',
    '&#152;' => '&#732;',
    '&#153;' => '&#8482;',
    '&#154;' => '&#353;',
    '&#155;' => '&#8250;',
    '&#156;' => '&#339;',
    '&#157;' => '',
    '&#158;' => '&#382;',
    '&#159;' => '&#376;',
    );

    if (strpos($content, '&#1') !== false) {
        $content = strtr($content, $wp_htmltranswinuni);
    }

    return $content;
}

/**
 * Checks to see if a string is utf8 encoded.
 *
 * NOTE: This function checks for 5-Byte sequences, UTF8
 *       has Bytes Sequences with a maximum length of 4.
 *
 * @author bmorel at ssi dot fr (modified)
 * @since  1.0.0
 *
 * @param  string $str The string to be checked
 * @return bool True if $str fits a UTF-8 model, false otherwise.
 */
function seems_utf8($str)
{
    mbstring_binary_safe_encoding();
    $length = strlen($str);
    reset_mbstring_encoding();
    for ($i = 0; $i < $length; $i++) {
        $c = ord($str[ $i ]);
        if ($c < 0x80) {
            $n = 0; // 0bbbbbbb
        } elseif (( $c & 0xE0 ) == 0xC0) {
            $n = 1; // 110bbbbb
        } elseif (( $c & 0xF0 ) == 0xE0) {
            $n = 2; // 1110bbbb
        } elseif (( $c & 0xF8 ) == 0xF0) {
            $n = 3; // 11110bbb
        } elseif (( $c & 0xFC ) == 0xF8) {
            $n = 4; // 111110bb
        } elseif (( $c & 0xFE ) == 0xFC) {
            $n = 5; // 1111110b
        } else {
            return false; // Does not match any model.
        }
        for ($j = 0; $j < $n; $j++) { // n bytes matching 10bbbbbb follow ?
            if (( ++$i == $length ) || ( ( ord($str[ $i ]) & 0xC0 ) != 0x80 )) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Converts a number of special characters into their HTML entities.
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to encode " to
 * &quot;, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
 *
 * @since  1.0.0
 * @access private
 *
 * @param  string       $string        The text which is to be encoded.
 * @param  int|string   $quote_style   Optional. Converts double quotes if set to ENT_COMPAT,
 *                                     both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES.
 *                                     Converts single and double quotes, as well as converting HTML
 *                                     named entities (that are not also XML named entities) to their
 *                                     code points if set to ENT_XML1. Also compatible with old values;
 *                                     converting single quotes if set to 'single',
 *                                     double if set to 'double' or both if otherwise set.
 *                                     Default is ENT_NOQUOTES.
 * @param  false|string $charset       Optional. The character encoding of the string. Default false.
 * @param  bool         $double_encode Optional. Whether to encode existing HTML entities. Default false.
 * @return string The encoded text with HTML entities.
 */
function _wp_specialchars($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false)
{
    $string = (string) $string;

    if (0 === strlen($string)) {
        return '';
    }

    // Don't bother if there are no specialchars - saves some processing.
    if (! preg_match('/[&<>"\']/', $string)) {
        return $string;
    }

    // Account for the previous behaviour of the function when the $quote_style is not an accepted value.
    if (empty($quote_style)) {
        $quote_style = ENT_NOQUOTES;
    } elseif (ENT_XML1 === $quote_style) {
        $quote_style = ENT_QUOTES | ENT_XML1;
    } elseif (! in_array($quote_style, array( ENT_NOQUOTES, ENT_COMPAT, ENT_QUOTES, 'single', 'double' ), true)) {
        $quote_style = ENT_QUOTES;
    }

    // Store the site charset as a static to avoid multiple calls to wp_load_alloptions().
    if (! $charset) {
        $charset = SITE_CHARSET;
    }

    if (in_array($charset, array( 'utf8', 'utf-8', 'UTF8' ), true)) {
        $charset = 'UTF-8';
    }

    $_quote_style = $quote_style;

    if ('double' === $quote_style) {
        $quote_style  = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } elseif ('single' === $quote_style) {
        $quote_style = ENT_NOQUOTES;
    }

    $string = htmlspecialchars($string, $quote_style, $charset, $double_encode);

    // Back-compat.
    if ('single' === $_quote_style) {
        $string = str_replace("'", '&#039;', $string);
    }

    return $string;
}

/**
 * Converts a number of HTML entities into their special characters.
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to decode " entities,
 * or ENT_QUOTES to do both " and '. Default is ENT_NOQUOTES where no quotes are decoded.
 *
 * @since 1.0.0
 *
 * @param  string     $string      The text which is to be decoded.
 * @param  string|int $quote_style Optional. Converts double quotes if set to ENT_COMPAT,
 *                                 both single and double if set to ENT_QUOTES or
 *                                 none if set to ENT_NOQUOTES.
 *                                 Also compatible with old _wp_specialchars() values;
 *                                 converting single quotes if set to 'single',
 *                                 double if set to 'double' or both if otherwise set.
 *                                 Default is ENT_NOQUOTES.
 * @return string The decoded text without HTML entities.
 */
function wp_specialchars_decode($string, $quote_style = ENT_NOQUOTES)
{
    $string = (string) $string;

    if (0 === strlen($string)) {
        return '';
    }

    // Don't bother if there are no entities - saves a lot of processing.
    if (strpos($string, '&') === false) {
        return $string;
    }

    // Match the previous behaviour of _wp_specialchars() when the $quote_style is not an accepted value.
    if (empty($quote_style)) {
        $quote_style = ENT_NOQUOTES;
    } elseif (! in_array($quote_style, array( 0, 2, 3, 'single', 'double' ), true)) {
        $quote_style = ENT_QUOTES;
    }

    // More complete than get_html_translation_table( HTML_SPECIALCHARS ).
    $single      = array(
    '&#039;' => '\'',
    '&#x27;' => '\'',
    );
    $single_preg = array(
    '/&#0*39;/'   => '&#039;',
    '/&#x0*27;/i' => '&#x27;',
    );
    $double      = array(
    '&quot;' => '"',
    '&#034;' => '"',
    '&#x22;' => '"',
    );
    $double_preg = array(
    '/&#0*34;/'   => '&#034;',
    '/&#x0*22;/i' => '&#x22;',
    );
    $others      = array(
    '&lt;'   => '<',
    '&#060;' => '<',
    '&gt;'   => '>',
    '&#062;' => '>',
    '&amp;'  => '&',
    '&#038;' => '&',
    '&#x26;' => '&',
    );
    $others_preg = array(
    '/&#0*60;/'   => '&#060;',
    '/&#0*62;/'   => '&#062;',
    '/&#0*38;/'   => '&#038;',
    '/&#x0*26;/i' => '&#x26;',
    );

    if (ENT_QUOTES === $quote_style) {
        $translation      = array_merge($single, $double, $others);
        $translation_preg = array_merge($single_preg, $double_preg, $others_preg);
    } elseif (ENT_COMPAT === $quote_style || 'double' === $quote_style) {
        $translation      = array_merge($double, $others);
        $translation_preg = array_merge($double_preg, $others_preg);
    } elseif ('single' === $quote_style) {
        $translation      = array_merge($single, $others);
        $translation_preg = array_merge($single_preg, $others_preg);
    } elseif (ENT_NOQUOTES === $quote_style) {
        $translation      = $others;
        $translation_preg = $others_preg;
    }

    // Remove zero padding on numeric entities.
    $string = preg_replace(array_keys($translation_preg), array_values($translation_preg), $string);

    // Replace characters according to translation table.
    return strtr($string, $translation);
}

/**
 * Checks for invalid UTF8 in a string.
 *
 * @since 1.0.0
 *
 * @param  string $string The text which is to be checked.
 * @param  bool   $strip  Optional. Whether to attempt to strip out invalid UTF8. Default false.
 * @return string The checked text.
 */
function wp_check_invalid_utf8($string, $strip = false)
{
    $string = (string) $string;

    if (0 === strlen($string)) {
        return '';
    }

    // Store the site charset as a static to avoid multiple calls to get_option().
    static $is_utf8 = null;
    if (! isset($is_utf8)) {
        $is_utf8 = in_array(SITE_CHARSET, array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ), true);
    }
    if (! $is_utf8) {
        return $string;
    }

    // Check for support for utf8 in the installed PCRE incrary once and store the result in a static.
    static $utf8_pcre = null;
    if (! isset($utf8_pcre)) {
     // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
        $utf8_pcre = @preg_match('/^./u', 'a');
    }
    // We can't demand utf8 in the PCRE installation, so just return the string in those cases.
    if (! $utf8_pcre) {
        return $string;
    }

	// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- preg_match fails when it encounters invalid UTF8 in $string.
    if (1 === @preg_match('/^./us', $string)) {
        return $string;
    }

    // Attempt to strip the bad chars if requested (not recommended).
    if ($strip && function_exists('iconv')) {
        return iconv('utf-8', 'utf-8', $string);
    }

    return '';
}

/**
 * Add leading zeros when necessary.
 *
 * If you set the threshold to '4' and the number is '10', then you will get
 * back '0010'. If you set the threshold to '4' and the number is '5000', then you
 * will get back '5000'.
 *
 * Uses sprintf to append the amount of zeros based on the $threshold parameter
 * and the size of the number. If the number is large enough, then no zeros will
 * be appended.
 *
 * @since 1.0.0
 *
 * @param  int $number    Number to append zeros to if not greater than threshold.
 * @param  int $threshold Digit places number needs to be to not have zeros added.
 * @return string Adds leading zeros to number if needed.
 */
function zeroise($number, $threshold)
{
    return sprintf('%0' . $threshold . 's', $number);
}

/**
 * Appends a trailing slash.
 *
 * Will remove trailing forward and backslashes if it exists already before adding
 * a trailing forward slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 1.0.0
 *
 * @param  string $string What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function trailingslashit($string)
{
    return untrailingslashit($string) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 1.0.0
 *
 * @param  string $string What to remove the trailing slashes from.
 * @return string String without the trailing slashes.
 */
function untrailingslashit($string)
{
    return rtrim($string, '/\\');
}

/**
 * Adds slashes to a string or recursively adds slashes to strings within an array.
 *
 * Slashes will first be removed if magic_quotes_gpc is set, see {@link
 * https://www.php.net/magic_quotes} for more details.
 *
 * @since 1.0.0
 *
 * @param  string|array $gpc String or array of data to slash.
 * @return string|array Slashed `$gpc`.
 */
function addslashes_gpc($gpc)
{
    return wp_slash($gpc);
}

/**
 * Navigates through an array, object, or scalar, and removes slashes from the values.
 *
 * @since 1.0.0
 *
 * @param  mixed $value The value to be stripped.
 * @return mixed Stripped value.
 */
function stripslashes_deep($value)
{
    return map_deep($value, 'stripslashes_from_strings_only');
}

/**
 * Callback function for `stripslashes_deep()` which strips slashes from strings.
 *
 * @since 1.0.0
 *
 * @param  mixed $value The array or string to be stripped.
 * @return mixed The stripped value.
 */
function stripslashes_from_strings_only($value)
{
    return is_string($value) ? stripslashes($value) : $value;
}

/**
 * Navigates through an array, object, or scalar, and encodes the values to be used in a URL.
 *
 * @since 1.0.0
 *
 * @param  mixed $value The array or string to be encoded.
 * @return mixed The encoded value.
 */
function urlencode_deep($value)
{
    return map_deep($value, 'urlencode');
}

/**
 * Navigates through an array, object, or scalar, and raw-encodes the values to be used in a URL.
 *
 * @since 1.0.0
 *
 * @param  mixed $value The array or string to be encoded.
 * @return mixed The encoded value.
 */
function rawurlencode_deep($value)
{
    return map_deep($value, 'rawurlencode');
}

/**
 * Navigates through an array, object, or scalar, and decodes URL-encoded values
 *
 * @since 1.0.0
 *
 * @param  mixed $value The array or string to be decoded.
 * @return mixed The decoded value.
 */
function urldecode_deep($value)
{
    return map_deep($value, 'urldecode');
}

/**
 * Converts email addresses characters to HTML entities to block spam bots.
 *
 * @since 1.0.0
 *
 * @param  string $email_address Email address.
 * @param  int    $hex_encoding  Optional. Set to 1 to enable hex encoding.
 * @return string Converted email address.
 */
function antispambot($email_address, $hex_encoding = 0)
{
    $email_no_spam_address = '';
    for ($i = 0, $len = strlen($email_address); $i < $len; $i++) {
        $j = rand(0, 1 + $hex_encoding);
        if (0 == $j) {
            $email_no_spam_address .= '&#' . ord($email_address[ $i ]) . ';';
        } elseif (1 == $j) {
            $email_no_spam_address .= $email_address[ $i ];
        } elseif (2 == $j) {
            $email_no_spam_address .= '%' . zeroise(dechex(ord($email_address[ $i ])), 2);
        }
    }

    return str_replace('@', '&#64;', $email_no_spam_address);
}

/**
 * Verifies that an email is valid.
 *
 * Does not grok i18n domains. Not RFC compliant.
 *
 * @since 1.0.0
 *
 * @param  string $email Email address to verify.
 * @return string|false Valid email address on success, false on failure.
 */
function is_email($email)
{
    // Test for the minimum length the email can be.
    if (strlen($email) < 6) {
        return false;
    }

    // Test for an @ character after the first position.
    if (strpos($email, '@', 1) === false) {
        return false;
    }

    // Split out the local and domain parts.
    list( $local, $domain ) = explode('@', $email, 2);

    // LOCAL PART
    // Test for invalid characters.
    if (! preg_match('/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local)) {
        return false;
    }

    // DOMAIN PART
    // Test for sequences of periods.
    if (preg_match('/\.{2,}/', $domain)) {
        return false;
    }

    // Test for leading and trailing periods and whitespace.
    if (trim($domain, " \t\n\r\0\x0B.") !== $domain) {
        return false;
    }

    // Split the domain into subs.
    $subs = explode('.', $domain);

    // Assume the domain will have at least two subs.
    if (2 > count($subs)) {
        return false;
    }

    // Loop through each sub.
    foreach ($subs as $sub) {
        // Test for leading and trailing hyphens and whitespace.
        if (trim($sub, " \t\n\r\0\x0B-") !== $sub) {
            return false;
        }

        // Test for invalid characters.
        if (! preg_match('/^[a-z0-9-]+$/i', $sub)) {
            return false;
        }
    }

    // Congratulations, your email made it!
    return $email;
}

/**
 * Given a date in the timezone of the site, returns that date in UTC.
 *
 * Requires and returns a date in the Y-m-d H:i:s format.
 * Return format can be overridden using the $format parameter.
 *
 * @since 1.0.0
 *
 * @param  string $string The date to be converted, in the timezone of the site.
 * @param  string $format The format string for the returned date. Default 'Y-m-d H:i:s'.
 * @return string Formatted version of the date, in UTC.
 */
function get_gmt_from_date($string, $format = 'Y-m-d H:i:s')
{
    $datetime = date_create($string, wp_timezone());

    if (false === $datetime) {
        return gmdate($format, 0);
    }

    return $datetime->setTimezone(new DateTimeZone('UTC'))->format($format);
}

/**
 * Given a date in UTC or GMT timezone, returns that date in the timezone of the site.
 *
 * Requires a date in the Y-m-d H:i:s format.
 * Default return format of 'Y-m-d H:i:s' can be overridden using the `$format` parameter.
 *
 * @since 1.0.0
 *
 * @param  string $string The date to be converted, in UTC or GMT timezone.
 * @param  string $format The format string for the returned date. Default 'Y-m-d H:i:s'.
 * @return string Formatted version of the date, in the site's timezone.
 */
function get_date_from_gmt($string, $format = 'Y-m-d H:i:s')
{
    $datetime = date_create($string, new DateTimeZone('UTC'));

    if (false === $datetime) {
        return gmdate($format, 0);
    }

    return $datetime->setTimezone(wp_timezone())->format($format);
}

/**
 * Given an ISO 8601 timezone, returns its UTC offset in seconds.
 *
 * @since 1.0.0
 *
 * @param  string $timezone Either 'Z' for 0 offset or '±hhmm'.
 * @return int|float The offset in seconds.
 */
function iso8601_timezone_to_offset($timezone)
{
    // $timezone is either 'Z' or '[+|-]hhmm'.
    if ('Z' === $timezone) {
        $offset = 0;
    } else {
        $sign    = ( '+' === substr($timezone, 0, 1) ) ? 1 : -1;
        $hours   = (int) substr($timezone, 1, 2);
        $minutes = (int) substr($timezone, 3, 4) / 60;
        $offset  = $sign * HOUR_IN_SECONDS * ( $hours + $minutes );
    }
    return $offset;
}

/**
 * Given an ISO 8601 (Ymd\TH:i:sO) date, returns a MySQL DateTime (Y-m-d H:i:s) format used by post_date[_gmt].
 *
 * @since 1.0.0
 *
 * @param  string $date_string Date and time in ISO 8601 format {@link https://en.wikipedia.org/wiki/ISO_8601}.
 * @param  string $timezone    Optional. If set to 'gmt' returns the result in UTC. Default 'user'.
 * @return string|false The date and time in MySQL DateTime format - Y-m-d H:i:s, or false on failure.
 */
function iso8601_to_datetime($date_string, $timezone = 'user')
{
    $timezone    = strtolower($timezone);
    $wp_timezone = wp_timezone();
    $datetime    = date_create($date_string, $wp_timezone); // Timezone is ignored if input has one.

    if (false === $datetime) {
        return false;
    }

    if ('gmt' === $timezone) {
        return $datetime->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    if ('user' === $timezone) {
        return $datetime->setTimezone($wp_timezone)->format('Y-m-d H:i:s');
    }

    return false;
}

/**
 * Perform a deep string replace operation to ensure the values in $search are no longer present
 *
 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
 * str_replace would return
 *
 * @since  1.0.0
 * @access private
 *
 * @param  string|array $search  The value being searched for, otherwise known as the needle.
 *                               An array may be used to designate multiple needles.
 * @param  string       $subject The string being searched and replaced on, otherwise known as the haystack.
 * @return string The string with the replaced values.
 */
function _deep_replace($search, $subject)
{
    $subject = (string) $subject;

    $count = 1;
    while ($count) {
        $subject = str_replace($search, '', $subject, $count);
    }

    return $subject;
}

/**
 * Escaping for HTML blocks.
 *
 * @since 1.0.0
 *
 * @param  string $text
 * @return string
 */
function esc_html($text)
{
    $safe_text = wp_check_invalid_utf8($text);
    $safe_text = _wp_specialchars($safe_text, ENT_QUOTES);
    return $safe_text;
}

/**
 * Escaping for HTML attributes.
 *
 * @since 1.0.0
 *
 * @param  string $text
 * @return string
 */
function esc_attr($text)
{
    $safe_text = wp_check_invalid_utf8($text);
    $safe_text = _wp_specialchars($safe_text, ENT_QUOTES);
    return $safe_text;
}

/**
 * Escaping for textarea values.
 *
 * @since 1.0.0
 *
 * @param  string $text
 * @return string
 */
function esc_textarea($text)
{
    $safe_text = htmlspecialchars($text, ENT_QUOTES, SITE_CHARSET);
    return $safe_text;
}

/**
 * Escape single quotes, htmlspecialchar " < > &, and fix line endings.
 *
 * Escapes text strings for echoing in JS. It is intended to be used for inline JS
 * (in a tag attribute, for example onclick="..."). Note that the strings have to
 * be in single quotes. The {@see 'js_escape'} filter is also applied here.
 *
 * @since 1.0.0
 *
 * @param  string $text The text to be escaped.
 * @return string Escaped text.
 */
function esc_js($text)
{
    $safe_text = wp_check_invalid_utf8($text);
    $safe_text = _wp_specialchars($safe_text, ENT_COMPAT);
    $safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
    $safe_text = str_replace("\r", '', $safe_text);
    $safe_text = str_replace("\n", '\\n', addslashes($safe_text));
    
    return $safe_text;
}

/**
 * Convert full URL paths to absolute paths.
 *
 * Removes the http or https protocols and the domain. Keeps the path '/' at the
 * beginning, so it isn't a true relative link, but from the web root base.
 *
 * @since 1.0.0
 *
 * @param  string $link Full URL path.
 * @return string Absolute path.
 */
function wp_make_link_relative($link)
{
    return preg_replace('|^(https?:)?//[^/]+(/?.*)|i', '$2', $link);
}

/**
 * Maps a function to all non-iterable elements of an array or an object.
 *
 * This is similar to `array_walk_recursive()` but acts upon objects too.
 *
 * @since 1.0.0
 *
 * @param  mixed    $value    The array, object, or scalar.
 * @param  callable $callback The function to map onto $value.
 * @return mixed The value with the callback applied to all non-arrays and non-objects inside it.
 */
function map_deep($value, $callback)
{
    if (is_array($value)) {
        foreach ($value as $index => $item) {
            $value[ $index ] = map_deep($item, $callback);
        }
    } elseif (is_object($value)) {
        $object_vars = get_object_vars($value);
        foreach ($object_vars as $property_name => $property_value) {
            $value->$property_name = map_deep($property_value, $callback);
        }
    } else {
        $value = call_user_func($callback, $value);
    }

    return $value;
}

/**
 * Parses a string into variables to be stored in an array.
 *
 * @since 1.0.0
 *
 * @param string $string The string to be parsed.
 * @param array  $array  Variables will be stored in this array.
 */
function wp_parse_str($string, &$array)
{
    parse_str((string) $string, $array);
}

function wp_parse_args($args, $defaults = array())
{
    if (is_object($args)) {
        $parsed_args = get_object_vars($args);
    } elseif (is_array($args)) {
        $parsed_args =& $args;
    } else {
        wp_parse_str($args, $parsed_args);
    }
 
    if (is_array($defaults) && $defaults) {
        return array_merge($defaults, $parsed_args);
    }
    return $parsed_args;
}

/**
 * Convert lone less than signs.
 *
 * KSES already converts lone greater than signs.
 *
 * @since 1.0.0
 *
 * @param  string $text Text to be converted.
 * @return string Converted text.
 */
function wp_pre_kses_less_than($text)
{
    return preg_replace_callback('%<[^>]*?((?=<)|>|$)%', 'wp_pre_kses_less_than_callback', $text);
}

/**
 * Callback function used by preg_replace.
 *
 * @since 1.0.0
 *
 * @param  string[] $matches Populated by matches to preg_replace.
 * @return string The text returned after esc_html if needed.
 */
function wp_pre_kses_less_than_callback($matches)
{
    if (false === strpos($matches[0], '>')) {
        return esc_html($matches[0]);
    }
    return $matches[0];
}

/**
 * Safely extracts not more than the first $count characters from HTML string.
 *
 * UTF-8, tags and entities safe prefix extraction. Entities inside will *NOT*
 * be counted as one character. For example &amp; will be counted as 4, &lt; as
 * 3, etc.
 *
 * @since 1.0.0
 *
 * @param  string $str   String to get the excerpt from.
 * @param  int    $count Maximum number of characters to take.
 * @param  string $more  Optional. What to append if $str needs to be trimmed. Defaults to empty string.
 * @return string The excerpt.
 */
function wp_html_excerpt($str, $count, $more = null)
{
    if (null === $more) {
        $more = '';
    }

    $str     = wp_strip_all_tags($str, true);
    $excerpt = mb_substr($str, 0, $count);

    // Remove part of an entity at the end.
    $excerpt = preg_replace('/&[^;\s]{0,6}$/', '', $excerpt);
    if ($str != $excerpt) {
        $excerpt = trim($excerpt) . $more;
    }

    return $excerpt;
}

/**
 * Properly strip all HTML tags including script and style
 *
 * This differs from strip_tags() because it removes the contents of
 * the `<script>` and `<style>` tags. E.g. `strip_tags( '<script>something</script>' )`
 * will return 'something'. wp_strip_all_tags will return ''
 *
 * @since 1.0.0
 *
 * @param  string $string        String containing HTML tags
 * @param  bool   $remove_breaks Optional. Whether to remove left over line breaks and white space chars
 * @return string The processed string.
 */
function wp_strip_all_tags($string, $remove_breaks = false)
{
    $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
    $string = strip_tags($string);

    if ($remove_breaks) {
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
    }

    return trim($string);
}

/**
 * A more controlled version of wp_strip_all_tags.
 *
 * @param string $string String containing HTML tags
 * @param array  $args   {
 * @type  bool     $purge_script_n_style     Whether to remove all script and style elements from given string. Default true.
 * @type  bool     $remove_breaks             Whether to remove left over line breaks and white space chars. Default false.
 * @type  array    $allowed_tags            All but these tags will be removed.
 * }
 */
function lg_strip_tags($string, $args = '')
{
    $defaults = [
    'purge_script_n_style' => true,
    'remove_breaks' => false,
    'allowed_tags' => [],
    ];

    $args = wp_parse_args($args, $defaults);
    if ($args[ 'purge_script_n_style' ]) {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
    }

    $string = strip_tags($string, $args[ 'allowed_tags' ]);

    if ($args[ 'remove_breaks' ]) {
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
    }

    return trim($string);
}

/**
 * Sanitizes a string from user input or from the database.
 *
 * - Checks for invalid UTF-8,
 * - Converts single `<` characters to entities
 * - Strips all tags
 * - Removes line breaks, tabs, and extra whitespace
 * - Strips octets
 *
 * @since 1.0.0
 *
 * @see sanitize_textarea_field()
 * @see wp_check_invalid_utf8()
 * @see wp_strip_all_tags()
 *
 * @param  string $str String to sanitize.
 * @return string Sanitized string.
 */
function sanitize_text_field($str)
{
    $filtered = _sanitize_text_fields($str, false);

    return $filtered;
}

/**
 * Sanitizes a multiline string from user input or from the database.
 *
 * The function is like sanitize_text_field(), but preserves
 * new lines (\n) and other whitespace, which are legitimate
 * input in textarea elements.
 *
 * @see sanitize_text_field()
 *
 * @since 1.0.0
 *
 * @param  string $str String to sanitize.
 * @return string Sanitized string.
 */
function sanitize_textarea_field($str)
{
    $filtered = _sanitize_text_fields($str, true);

    return $filtered;
}

/**
 * Sanitizes a multiline html from user input or from the database.
 *
 * The function is like sanitize_text_field(), but preserves a list of allowed html tags,
 * new lines (\n) and other whitespace, which are legitimate input in textarea elements.
 *
 * @see sanitize_text_field()
 *
 * @since 1.0.0
 *
 * @param  string $str String to sanitize.
 * @return string Sanitized string.
 */
function sanitize_html_textarea_field($str)
{
    $keep_newlines = true;

    if (is_object($str) || is_array($str)) {
        return '';
    }

    $str = (string) $str;

    $filtered = wp_check_invalid_utf8($str);

    if (strpos($filtered, '<') !== false) {
        $filtered = wp_pre_kses_less_than($filtered);
        // This will strip extra whitespace for us.
        // $filtered = wp_strip_all_tags( $filtered, false );
        $filtered = lg_strip_tags(
            $filtered,
            [
            'purge_script_n_style' => true,
            'allowed_tags' => [
            'strong',
            'small',
            'table',
            'span',
            'abbr',
            'code',
            'pre',
            'div',
            'img',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'ol',
            'ul',
            'li',
            'em',
            'hr',
            'br',
            'tr',
            'td',
            'p',
            'a',
            'b',
            'i',
            ]
            ]
        );

        // Use HTML entities in a special case to make sure no later
        // newline stripping stage could lead to a functional tag.
        $filtered = str_replace("<\n", "&lt;\n", $filtered);
    }

    if (! $keep_newlines) {
        $filtered = preg_replace('/[\r\n\t ]+/', ' ', $filtered);
    }
    $filtered = trim($filtered);

    $found = false;
    while (preg_match('/%[a-f0-9]{2}/i', $filtered, $match)) {
        $filtered = str_replace($match[0], '', $filtered);
        $found    = true;
    }

    if ($found) {
        // Strip out the whitespace that may now exist after removing the octets.
        $filtered = trim(preg_replace('/ +/', ' ', $filtered));
    }

    return $filtered;
}

/**
 * Internal helper function to sanitize a string from user input or from the db
 *
 * @since  1.0.0
 * @access private
 *
 * @param  string $str           String to sanitize.
 * @param  bool   $keep_newlines Optional. Whether to keep newlines. Default: false.
 * @return string Sanitized string.
 */
function _sanitize_text_fields($str, $keep_newlines = false)
{
    if (is_object($str) || is_array($str)) {
        return '';
    }

    $str = (string) $str;

    $filtered = wp_check_invalid_utf8($str);

    if (strpos($filtered, '<') !== false) {
        $filtered = wp_pre_kses_less_than($filtered);
        // This will strip extra whitespace for us.
        $filtered = wp_strip_all_tags($filtered, false);

        // Use HTML entities in a special case to make sure no later
        // newline stripping stage could lead to a functional tag.
        $filtered = str_replace("<\n", "&lt;\n", $filtered);
    }

    if (! $keep_newlines) {
        $filtered = preg_replace('/[\r\n\t ]+/', ' ', $filtered);
    }
    $filtered = trim($filtered);

    $found = false;
    while (preg_match('/%[a-f0-9]{2}/i', $filtered, $match)) {
        $filtered = str_replace($match[0], '', $filtered);
        $found    = true;
    }

    if ($found) {
        // Strip out the whitespace that may now exist after removing the octets.
        $filtered = trim(preg_replace('/ +/', ' ', $filtered));
    }

    return $filtered;
}

/**
 * Sanitize a mime type
 *
 * @since 1.0.0
 *
 * @param  string $mime_type Mime type
 * @return string Sanitized mime type
 */
function sanitize_mime_type($mime_type)
{
    $sani_mime_type = preg_replace('/[^-+*.a-zA-Z0-9\/]/', '', $mime_type);
    return $sani_mime_type;
}

/**
 * Adds slashes to a string or recursively adds slashes to strings within an array.
 *
 * This should be used when preparing data for core API that expects slashed data.
 * This should not be used to escape data going directly into an SQL query.
 *
 * @since 1.0.0
 *
 * @param  string|array $value String or array of data to slash.
 * @return string|array Slashed `$value`.
 */
function wp_slash($value)
{
    if (is_array($value)) {
        $value = array_map('wp_slash', $value);
    }

    if (is_string($value)) {
        return addslashes($value);
    }

    return $value;
}

/**
 * Removes slashes from a string or recursively removes slashes from strings within an array.
 *
 * This should be used to remove slashes from data passed to core API that
 * expects data to be unslashed.
 *
 * @since 1.0.0
 *
 * @param  string|array $value String or array of data to unslash.
 * @return string|array Unslashed `$value`.
 */
function wp_unslash($value)
{
    return stripslashes_deep($value);
}

/**
 * Returns the regexp for common whitespace characters.
 *
 * By default, spaces include new lines, tabs, nbsp entities, and the UTF-8 nbsp.
 * This is designed to replace the PCRE \s sequence. In ticket #22692, that
 * sequence was found to be unreliable due to random inclusion of the A0 byte.
 *
 * @since 1.0.0
 *
 * @return string The spaces regexp.
 */
function wp_spaces_regexp()
{
    return '[\r\n\t ]|\xC2\xA0|&nbsp;';
}

/**
 * Ensures the given number is a positive integer.
 *
 * @return int
 */
function absint($maybeint)
{
    return abs((int) $maybeint);
}

/**
 * Check if the given string is serialized data.
 *
 * @param string  $data   The string to check
 * @param boolean $strict Whether to be strict about the end of the string. Default true
 *
 * @return boolean True if the string is serialized
 */
function is_serialized($data, $strict = true)
{
    // If it isn't a string, it isn't serialized.
    if (! is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' === $data) {
        return true;
    }
    if (strlen($data) < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        $lastc = substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        $semicolon = strpos($data, ';');
        $brace     = strpos($data, '}');
        // Either ; or } must exist.
        if (false === $semicolon && false === $brace) {
            return false;
        }
        // But neither must be in the first X characters.
        if (false !== $semicolon && $semicolon < 3) {
            return false;
        }
        if (false !== $brace && $brace < 4) {
            return false;
        }
    }
    $token = $data[0];
    switch ($token) {
        case 's':
            if ($strict) {
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
            } elseif (false === strpos($data, '"')) {
                return false;
            }
            // Or else fall through.
        case 'a':
        case 'O':
            return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b':
        case 'i':
        case 'd':
            $end = $strict ? '$' : '';
            return (bool) preg_match("/^{$token}:[0-9.E+-]+;$end/", $data);
    }
    return false;
}

/**
 * Unserialize data only if it was serialized.
 *
 * @param  string $data
 * @return mixed
 */
function maybe_unserialize($data)
{
    if (is_serialized($data)) { // Don't attempt to unserialize data that wasn't serialized going in.
        return @unserialize(trim($data));
    }
 
    return $data;
}

function maybe_serialize($data)
{
    if (is_array($data) || is_object($data)) {
        return serialize($data);
    }
 
    return $data;
}

/**
 * Truncate the string to given length.
 *
 * @param string  $str               Original String
 * @param int     $num_chars         Number of characters desired in output string( excluding the suffix )
 * @param string  $suffix_or_prefix  Appended or prepend to string if it was truncated. Default empty.
 * @param boolean $to_neareset_space Useful when you need complete words. Default false. When set to true, the result is resticted upto the last complete word.
 * @param boolean $truncate_from_end Whether to trucate from end. Default true. Passing false will trucate the string from begining.
 *
 * @return string
 */
function truncate_string($str, $num_chars, $suffix_or_prefix = '', $to_neareset_space = false, $truncate_from_end = true)
{
    if ($num_chars > strlen($str)) {
        return $str;
    }
 
    if ($truncate_from_end) {
        $str = substr($str, 0, $num_chars);
    } else {
        $str = substr($str, $num_chars * -1);
    }
    
    if ($to_neareset_space) {
        if ($truncate_from_end) {
            $space_pos = strrpos($str, " ");
            if ($space_pos >= 0) {
                $str = substr($str, 0, $space_pos);
            }
        } else {
            $space_pos = strpos($str, " ");
            if ($space_pos >= 0) {
                $str = substr($str, $space_pos);
            }
        }
    }
    
    /*$space_pos = strrpos( $str, " " );
    if ( $to_neareset_space && $space_pos >= 0 ) {
        $str = substr( $str, 0, strrpos( $str, " " ) );
    }*/
 
    return $truncate_from_end ? $str . $suffix_or_prefix : $suffix_or_prefix . $str;
}
