<?php

/**
 * Helper functions to generate navigation menu html.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

namespace RecyleBin\PhpSSS;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Generate html for navigation items.
 *
 * @param array $items Array of nav items.
 *                     Each item must be an array consisting of 'slug' and 'text' properties.
 *                     If an item has sub menu items, that can be specified in 'items' property. Example:
 *
 *                     [
 *
 *                     [ 'slug' => '', 'text' => 'Home' ],
 *
 *                     [ 'slug' => '#', 'text' => 'Services', 'items' => [ [ 'slug' => 'service-1', 'text' => 'Service 1' ], [ 'slug' => 'service-2', 'text' => 'Service 2' ] ] ],
 *
 *                     [ 'slug' =>'benefits-for-you', 'text' => 'Benefits For You' ],
 *
 *                     ]
 *
 *                     Items can have multiple level of nesting.
 *
 * @param array $args  {
 *     @var   int $level Do not pass this explicitly.
 *     @var   string $wrapper The html element for wrapper. Default 'ul'.
 *     @var   string $wrapper_class The css classes for wrapper element.
 *     @var   string $elmeent The html element for individual items. Default 'li'.
 *     @var   string $elmeent_class The css classes for individual items element.
 * }
 *
 * @return string Generated html.
 */
function generate_nav($items, $args = [])
{
    $url_paths = current_request()->getPathParts();
    $current_path = !empty($url_paths) ? implode('/', $url_paths) : '';

    for ($i=0; $i<count($items); $i++) {
        _nav_items_set_active_recursive($items[ $i ], $current_path);
    }

    return _generate_nav_recursive($items, $args);
}

/**
 * The internal implementation may change. Do not call this function directly. Use generate_nav instead.
 *
 * @param array $items Array of nav items.
 * @param array $args  options.
 *
 * @return string Generated html.
 *
 * @see    generate_nav
 * @access private
 */
function _generate_nav_recursive($items, $args = [])
{
    if (empty($items)) {
        return [
        'html' => '',
        'is_current' => false,
        ];
    }

    $defaults = [
    'level' => 0,
    'wrapper' => 'ul',
    'wrapper_class' => '',
    'element' => 'li',
    'element_class' => '',
    ];

    $args = wp_parse_args($args, $defaults);

    $url_paths = current_request()->getPathParts();
    $curr_path_part = !empty($url_paths) ? $url_paths[ 0 ] : '';

    $html = sprintf("<%s class='lg-nav level_%s %s'>", $args[ 'wrapper' ], $args[ 'level' ], $args[ 'wrapper_class' ]);

    foreach ($items as $details) {
        $item_classes = [];
        if (!empty($args[ 'element_class' ])) {
            $item_classes[] = $args[ 'element_class' ];
        }
        $subnav = '';

        if (isset($details[ 'items' ]) && !empty($details[ 'items' ])) {
            // has sub nav items
            $item_classes[] = 'has-children';

            $level = $args[ 'level' ] * 1;
            $level++;
            $new_args = $args;
            $new_args[ 'level' ] = $level;
            $subnav = _generate_nav_recursive($details[ 'items' ], $new_args);
        }

        $slug = $details[ 'slug' ] ? trailingslashit($details['slug']) : '';
        $url = strpos($slug, ':/') !== false || strpos($slug, 'www.') !== false ? $slug : HOME_URL . $slug;
        $link = sprintf("<a href='%s'>%s</a>", $url, $details[ 'text' ]);
        
        if (isset($details[ 'active' ]) && $details[ 'active' ]) {
            $item_classes[] = 'active';
        }
        $html .= sprintf("<{$args['element']} %s>%s %s</{$args['element']}>", ( !empty($item_classes) ? "class='". implode(' ', $item_classes) ."'" : '' ), $link, $subnav);
    }

    $html .= "</{$args['wrapper']}>";

    return $html;
}

/**
 * Set 'active' = true|false on current nav item as well as its parent, if the slug of current nav item matches with $current_path
 *
 * @param array $item
 * @param string $current_path
 *
 * @return boolean
 */
function _nav_items_set_active_recursive(&$item, $current_path = '')
{
    $is_active = $item[ 'slug' ] === $current_path;

    if (!$is_active && isset($item[ 'items' ]) && ! empty($item['items'])) {
        for ($i = 0; $i < count($item['items']); $i++) {
            $is_active = _nav_items_set_active_recursive($item['items'][$i], $current_path);
            if ($is_active) {
                break;
            }
        }
    }

    $item['active'] = $is_active;
    return $is_active;
}
