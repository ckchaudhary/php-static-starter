<?php

/**
 * Opening parts of the html output.
 *
 * @package PhpSSS
 * @subpackage Templates
 * @author  @ckchaudhary
 * @since   1.0.0
 */

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php header_metas(); ?>
        <?php header_assets(); ?>
         
        <?php get_template_part('parts/before-closing-head-tag');?>
    </head>
    <body class="<?php body_class();?>" >
        <?php get_template_part('parts/after-opening-body-tag');?>