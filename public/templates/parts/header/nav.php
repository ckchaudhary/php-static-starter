<?php

/**
 * Generate navigation menu.
 *
 * @package PhpSSS
 * @subpackage Templates
 * @author  @ckchaudhary
 * @since   1.0.0
 */

$nav_items = [
    [
        'slug'  => '',
        'text'  => 'Home',
    ],

    [
        'slug'  => 'about-us',
        'text'  => 'About',
    ],

    [
        'slug' => 'services',
        'text' => 'Services',
        // Sub menu items?
        /*'items'   => [
            [ 'slug' => 'services/one', 'text' => 'One' ],
            [ 'slug' => 'services/two', 'text' => 'Two' ],
        ]*/
    ],

    [ 'slug' =>'contact-us', 'text' => 'Contact' ],
];

?>
<nav id="nav-content" class="w-full flex-grow lg:flex lg:items-center lg:w-auto hidden lg:block mt-2 lg:mt-0 md:bg-transparent z-20 bg-gray-100">
    <?php echo \RecyleBin\PhpSSS\generate_nav($nav_items);?>
</nav>