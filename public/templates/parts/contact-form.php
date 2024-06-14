<?php

/**
 * A contact form.
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

// we may also need google recaptcha
if (defined('GOOGLE_RECAPTCHA_V2_SITE_KEY') && ! empty(GOOGLE_RECAPTCHA_V2_SITE_KEY)) {
    \add_filter('load_recaptcha_script', function () {
        return true;
    });
}
?>
<div class="container px-4">
    
    <form method="POST" action="<?php echo HOME_URL;?>ajax/contact-form/" id="frm_contact" class="max-w-screen-lg mt-8 mb-2 w-80 sm:w-96 mx-auto md:mx-0">
        <input type="hidden" name="_nonce" value="<?php echo create_nonce('contact-form');?>">

        <div class="flex flex-col gap-6 mb-1">
            <h6 class="block -mb-3 font-sans text-base antialiased font-semibold leading-relaxed tracking-normal text-blue-gray-900 text-left">
                Your Name
            </h6>
            <div class="field relative h-11 w-full min-w-[200px]">
                <input type='text' name='y-name' required placeholder="John Doe" class="peer h-full w-full rounded-md border border-blue-gray-200 border-t-transparent !border-t-blue-gray-200 bg-transparent px-3 py-3 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 focus:border-2 focus:border-gray-900 focus:border-t-transparent focus:!border-t-gray-900 focus:outline-0 disabled:border-0 disabled:bg-blue-gray-50" />
            </div>

            <h6 class="block -mb-3 font-sans text-base antialiased font-semibold leading-relaxed tracking-normal text-blue-gray-900 text-left">
                Your Email
            </h6>
            <div class="field relative h-11 w-full min-w-[200px]">
                <input type='email' name='y-email' required placeholder="name@mail.com" class="peer h-full w-full rounded-md border border-blue-gray-200 border-t-transparent !border-t-blue-gray-200 bg-transparent px-3 py-3 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 focus:border-2 focus:border-gray-900 focus:border-t-transparent focus:!border-t-gray-900 focus:outline-0 disabled:border-0 disabled:bg-blue-gray-50" />
            </div>

            <h6 class="block -mb-3 font-sans text-base antialiased font-semibold leading-relaxed tracking-normal text-blue-gray-900 text-left">Message</h6>
            <div class="field relative w-full min-w-[200px]">
                <textarea name='y-msg' required rows='5' placeholder="..hello there"
                class="peer w-full rounded-md border border-blue-gray-200 border-t-transparent !border-t-blue-gray-200 bg-transparent px-3 py-3 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 focus:border-2 focus:border-gray-900 focus:border-t-transparent focus:!border-t-gray-900 focus:outline-0 disabled:border-0 disabled:bg-blue-gray-50"></textarea>
            </div>
        </div>
        
        <?php if (defined('GOOGLE_RECAPTCHA_V2_SITE_KEY') && !empty(GOOGLE_RECAPTCHA_V2_SITE_KEY)) : ?>
            <div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_RECAPTCHA_V2_SITE_KEY;?>"></div>
        <?php endif; ?>

        <button class="mt-6 block w-full select-none rounded-lg bg-gray-900 py-3 px-6 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
            type="submit">
            <span class="st-normal align-middle">send</span>
            <span class="st-processing hidden align-middle">Processing...</span>
        </button>

        <div class="response my-4 hidden">
        </div>
    </form>
</div>