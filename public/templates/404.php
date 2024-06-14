<?php

/**
 * The template for 404 page.
 *
 * @package PhpSSS
 * @subpackage Templates
 * @author  @ckchaudhary
 * @since   1.0.0
 */

current_request()->setDetails([
    'title' => '404 - Page not found - My Website',
    //'meta-description'  => "some details about the site",
    //'meta-keywords'  => "keyword1, keyword-two",
    'body_classes' => [ '404', 'error' ],
]);
?>

<?php get_template_part('parts/header', '404');?>

<!--Container-->
<div class="container w-full md:max-w-4xl mx-auto pt-20">

    <div class="w-full px-4 md:px-6 text-xl text-gray-800 leading-normal">

        <div class="my-9 text-center">
            <p class="text-9xl text-gray-300 my-4 drop-shadow">404</p>
            <p class="text-6xl text-gray-900 my-4">Page not found!</p>

            <p class="my-20">
                <a href='<?php echo HOME_URL;?>' title='go to homepage' class="mt-6 select-none rounded-lg bg-gray-900 py-3 px-6 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">Go to homepage</a>
            </p>
        </div>
    </div>

</div>
<!--/container-->

<?php get_template_part('parts/footer', '404');?>