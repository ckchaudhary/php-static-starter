<?php

/**
 * The template for about-us page.
 *
 * @package PhpSSS
 * @subpackage Templates
 * @author  @ckchaudhary
 * @since   1.0.0
 */

current_request()->setDetails([
    'title' => 'About Us - My Website',
    //'meta-description'  => "some details about the site",
    //'meta-keywords'  => "keyword1, keyword-two",
    'body_classes' => [ 'page', 'about' ],
]);
?>

<?php get_template_part('parts/header', 'about');?>

<!--Container-->
<div class="container w-full md:max-w-4xl mx-auto pt-20">

    <div class="w-full px-4 md:px-6 text-xl text-gray-800 leading-normal">

        <!--Title-->
        <h1 class="page-title">About - My Website</h1>

        <!--Main Content-->


        <!--Lead Para-->
        <p class="py-6">
            👋 Lorem ipsum <a class="text-green-500 no-underline hover:underline" href="#">LIPSUM</a> dolor sit amet, consectetur adipiscing elit. Nam ac congue risus. Sed hendrerit tristique augue sed viverra. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Suspendisse in sagittis ligula, quis egestas leo.
        </p>

        <p class="py-6">Nam aliquet efficitur eleifend. Aenean cursus magna nec felis eleifend, non vestibulum ex fringilla. Aliquam interdum egestas tincidunt. Nullam porttitor est rhoncus ultricies blandit.</p>

        <p class="py-6">In sollicitudin consectetur mauris sed pharetra. Aenean facilisis tempor ultrices. Aliquam nec egestas dolor. Sed at dui molestie, malesuada lectus sit amet, scelerisque mi. Ut ut vestibulum nisl. Sed id fermentum lacus. Duis luctus cursus ornare. Morbi sodales mauris sagittis dolor eleifend convallis. Vestibulum eu ex et sem luctus rutrum. Integer eu ligula odio. Praesent at arcu at nunc tempus convallis nec sit amet leo. Etiam volutpat vitae tellus eu volutpat. Pellentesque libero mauris, rhoncus non risus eu, dignissim luctus neque. Quisque a nibh id mauris pharetra condimentum non ut eros.</p>


        <h1 class="py-2 text-6xl">Heading 1</h1>
        <h2 class="py-2 text-5xl">Heading 2</h2>
        <h3 class="py-2 text-4xl">Heading 3</h3>
        <h4 class="py-2 text-3xl">Heading 4</h4>
        <h5 class="py-2 text-2xl">Heading 5</h5>
        <h6 class="py-2 text-1xl">Heading 6</h6>

        <p class="py-6">Sed dignissim lectus ut tincidunt vulputate. Fusce tincidunt lacus purus, in mattis tortor sollicitudin pretium. Phasellus at diam posuere, scelerisque nisl sit amet, tincidunt urna. Cras nisi diam, pulvinar ut molestie eget, eleifend ac magna. Sed at lorem condimentum, dignissim lorem eu, blandit massa. Phasellus eleifend turpis vel erat bibendum scelerisque. Maecenas id risus dictum, rhoncus odio vitae, maximus purus. Etiam efficitur dolor in dolor molestie ornare. Aenean pulvinar diam nec neque tincidunt, vitae molestie quam fermentum. Donec ac pretium diam. Suspendisse sed odio risus. Nunc nec luctus nisi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis nec nulla eget sem dictum elementum.</p>
        <!--/ Main Content-->

    </div>

</div>
<!--/container-->

<?php get_template_part('parts/footer', 'about');?>