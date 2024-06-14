<?php
/**
 * Template tags etc.
 *
 * @package PhpSSS
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Prints information inside <head> section of document.
 * Information like <meta> tags, <title> tag, favicons etc.
 *
 * @return void
 */
function header_metas()
{
    ?>
    <meta charset="<?php echo SITE_CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo HOME_URL ?>apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo HOME_URL ?>favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo HOME_URL ?>favicon-16x16.png">

    <?php
    $title = current_request()->getDetails('title');
    echo "<title>". stripslashes($title) ."</title>";

    $meta_description = current_request()->getDetails('meta-description');
    if ($meta_description) {
        echo "<meta name='description' content='". esc_attr($meta_description) ."'>";
    }

    $meta_keywords = current_request()->getDetails('meta-keywords');
    if ($meta_keywords) {
        echo "<meta name='keywords' content='". esc_attr($meta_keywords) ."' >";
    }

    // @site-specific
    // @todo: Change this
    echo "<meta name='author' content='YourCompany' >";
}

/**
 * Loads assets like css, font files etc inside <head> element.
 *
 * @return void
 */
function header_assets()
{
    echo '<link rel="stylesheet" href="'. ASSETS_URL . 'css/style.css">';
}

/**
 * Loads assets in footer, near the </body> closing tag.
 *
 * @return void
 */
function footer_assets()
{
    $load_recaptcha = \apply_filters('load_recaptcha_script', false);
    if ($load_recaptcha) {
        ?><script src="https://www.google.com/recaptcha/api.js" async defer></script><?php
    }

    // jQuery
    echo '<script src="' . ASSETS_URL . 'js/jquery-3.6.0.min.js"></script>';

    // jQuery form
    $load_jquery_form = \apply_filters('load_jquery_form', false);
    if ($load_jquery_form) {
        ?><script src="https://cdn.jsdelivr.net/gh/jquery-form/form@4.3.0/dist/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script><?php
    }

    // Custom script
    echo '<script src="'. ASSETS_URL .'js/custom.js"></script>';

    $inline_script_nonce = inline_script_nonce();
    echo "<script nonce='". esc_attr($inline_script_nonce) ."'>var HOME_URL = '" . HOME_URL . "';</script>";
}

/**
 * Prints css classes for <body> tag.
 *
 * @return void
 */
function body_class()
{
    $body_classes = current_request()->getDetails('body_classes');
    $body_classes = \apply_filters('body_classes', $body_classes);
    if ($body_classes) {
        echo implode(' ', $body_classes);
    }
}

/**
 * Loads the given template.
 *
 * @param string $slug Name of the template file, without a leading '.php'.
 * @param string $name Name of the variant of template file, without a leading '.php'. Optional.
 *
 * @return void
 */
function get_template_part($slug, $name = null)
{
    $templates = array();
    $name      = (string) $name;
    if ('' !== $name) {
        $templates[] = "{$slug}-{$name}.php";
    }
 
    $templates[] = "{$slug}.php";

    if (! locate_template($templates, true, false)) {
        return false;
    }

    return true;
}

/**
 * Find the first matching file for given template paths.
 *
 * @param [type] $template_names Template file names with leading '.php'.
 * @param boolean $load Whether to load the template or just return its path. Default false.
 * @param boolean $require_once Whether to use include_once or include while loading the template file. Default true.
 *
 * @return string|void file path if $load is false.
 */
function locate_template($template_names, $load = false, $require_once = true)
{
    $located = '';
    foreach ((array) $template_names as $template_name) {
        if (! $template_name) {
            continue;
        }

        if (file_exists(ABSPATH . 'public/templates/' . $template_name)) {
            $located = ABSPATH . 'public/templates/' . $template_name;
            break;
        }
    }
 
    if ($load && '' !== $located) {
        if ($require_once) {
            include_once $located;
        } else {
            include $located;
        }
    }
 
    return $located;
}
