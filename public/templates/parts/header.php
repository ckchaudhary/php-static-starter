<?php

/**
 * The default header template part.
 *
 * @package PhpSSS
 * @subpackage Templates
 * @author  @ckchaudhary
 * @since   1.0.0
 */

?>
<?php get_template_part('parts/header/head');?>

<div id="header" class="fixed w-full z-10 top-0">

    <div id="progress" class="h-1 z-20 top-0"></div>

    <div class="w-full md:max-w-4xl mx-auto flex flex-wrap items-center justify-between mt-0 py-3">

        <div class="pl-4">
            <a class="text-gray-900 text-base no-underline hover:no-underline font-extrabold text-xl" href="#">
                Logo
            </a>
        </div>

        <div class="block lg:hidden pr-4">
            <button id="nav-toggle" class="flex items-center px-3 py-2 border rounded text-gray-500 border-gray-600 hover:text-gray-900 hover:border-green-500 appearance-none focus:outline-none">
                <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <title>Menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
                </svg>
            </button>
        </div>

        <?php get_template_part('parts/header/nav', 'main');?>
        
    </div>
</div>