<?php

/**
 * Parse and build query arguments.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

function add_query_arg(...$args)
{
    if (is_array($args[0])) {
        if (count($args) < 2 || false === $args[1]) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = $args[1];
        }
    } else {
        if (count($args) < 3 || false === $args[2]) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = $args[2];
        }
    }
 
    $frag = strstr($uri, '#');
    if ($frag) {
        $uri = substr($uri, 0, -strlen($frag));
    } else {
        $frag = '';
    }
 
    if (0 === stripos($uri, 'http://')) {
        $protocol = 'http://';
        $uri      = substr($uri, 7);
    } elseif (0 === stripos($uri, 'https://')) {
        $protocol = 'https://';
        $uri      = substr($uri, 8);
    } else {
        $protocol = '';
    }
 
    if (strpos($uri, '?') !== false) {
        list( $base, $query ) = explode('?', $uri, 2);
        $base                .= '?';
    } elseif ($protocol || strpos($uri, '=') === false) {
        $base  = $uri . '?';
        $query = '';
    } else {
        $base  = '';
        $query = $uri;
    }
 
    wp_parse_str($query, $qs);
    $qs = urlencode_deep($qs); // This re-URL-encodes things that were already in the query string.
    if (is_array($args[0])) {
        foreach ($args[0] as $k => $v) {
            $qs[ $k ] = $v;
        }
    } else {
        $qs[ $args[0] ] = $args[1];
    }
 
    foreach ($qs as $k => $v) {
        if (false === $v) {
            unset($qs[ $k ]);
        }
    }
 
    $ret = build_query($qs);
    $ret = trim($ret, '?');
    $ret = preg_replace('#=(&|$)#', '$1', $ret);
    $ret = $protocol . $base . $ret . $frag;
    $ret = rtrim($ret, '?');
    $ret = str_replace('?#', '#', $ret);
    return $ret;
}

function build_query($data)
{
    return _http_build_query($data, null, '&', '', false);
}

function _http_build_query($data, $prefix = null, $sep = null, $key = '', $urlencode = true)
{
    $ret = array();
 
    foreach ((array) $data as $k => $v) {
        if ($urlencode) {
            $k = urlencode($k);
        }
        if (is_int($k) && null != $prefix) {
            $k = $prefix . $k;
        }
        if (! empty($key)) {
            $k = $key . '%5B' . $k . '%5D';
        }
        if (null === $v) {
            continue;
        } elseif (false === $v) {
            $v = '0';
        }
 
        if (is_array($v) || is_object($v)) {
            array_push($ret, _http_build_query($v, '', $sep, $k, $urlencode));
        } elseif ($urlencode) {
            array_push($ret, $k . '=' . urlencode($v));
        } else {
            array_push($ret, $k . '=' . $v);
        }
    }
 
    if (null === $sep) {
        $sep = ini_get('arg_separator.output');
    }
 
    return implode($sep, $ret);
}
