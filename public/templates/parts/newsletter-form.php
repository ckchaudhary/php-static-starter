<?php

/**
 * A newsletter subscription form.
 * To demonstrate how to integrate forms.
 *
 * @package PhpSSS
 * @subpackage forms
 * @author  @ckchaudhary
 * @since   1.0.0
 */

// we need jquery form if this template is included
\add_filter('load_jquery_form', function () {
    return true;
});
?>
<div class="container px-4">
    <div class="font-sans bg-gradient-to-b from-green-100 to-gray-100 rounded-lg shadow-xl p-4 text-center">
        <h2 class="font-bold break-normal text-xl md:text-3xl">Subscribe to my Newsletter</h2>
        <h3 class="font-bold break-normal text-gray-600 text-sm md:text-base">Get the latest posts delivered right to your inbox</h3>
        <div class="w-full text-center pt-4">
            <form action="<?php echo HOME_URL;?>ajax/newsletter/" method="POST" id="frm_newsletter">
                <input type="hidden" name="_nonce" value="<?php echo create_nonce('newsletter-subscription');?>">

                <div class="max-w-xl mx-auto p-1 pr-0 flex flex-wrap items-center">
                    <input type="email" name="email" placeholder="youremail@example.com" class="flex-1 mt-4 appearance-none border border-gray-400 rounded shadow-md p-3 text-gray-600 mr-2 focus:outline-none">
                    <button type="submit" class="flex-1 mt-4 block md:inline-block appearance-none bg-green-500 text-white text-base font-semibold tracking-wider uppercase py-4 rounded shadow hover:bg-green-400">
                        <span class='st-normal'>Subscribe</span>
                        <span class='st-processing hidden'>Processing...</span>
                    </button>
                </div>

                <div class="response my-4 hidden"></div>
            </form>
        </div>
    </div>
</div>