<?php

/**
 * The template for contact us page.
 *
 * @package PhpSSS
 * @subpackage Templates
 * @author  @ckchaudhary
 * @since   1.0.0
 */

current_request()->setDetails([
    'title' => 'Contact Us - My Website',
    //'meta-description'  => "some details about the site",
    //'meta-keywords'  => "keyword1, keyword-two",
    'body_classes' => [ 'contact' ],
]);


?>

<?php get_template_part('parts/header', 'contact');?>

<!--Container-->
<div class="container w-full md:max-w-4xl mx-auto pt-20">

    <div class="w-full px-4 md:px-6 text-xl text-gray-800 leading-normal">

        <!--Title-->
        <h1 class="page-title">Contact Us</h1>

        <!--Main Content-->


        <!--Lead Para-->
        <p class="py-6">
            👋 Lorem ipsum <a class="text-green-500 no-underline hover:underline" href="#">LIPSUM</a> dolor sit amet, consectetur adipiscing elit. Nam ac congue risus. Sed hendrerit tristique augue sed viverra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Suspendisse in sagittis ligula, quis egestas leo.
        </p>

        <?php get_template_part('parts/contact-form');?>

        <p class="my-9">&nbsp;</p>

        <!--/ Main Content-->

    </div>

</div>
<!--/container-->

<?php get_template_part('parts/footer', 'contact');?>