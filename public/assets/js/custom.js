jQuery(($) => {
    let $body = $('body');

	// Newsletter subscription form
	let $nl_form = $('#frm_newsletter');
	if ($nl_form.length) {
		$nl_form.ajaxForm({
			beforeSubmit: function(formdata, $this_form, options) {
				if ($this_form.data('doing-ajax')) {
					return false;
				}

				let $res = $this_form.find( '.response' );
				$res.html('').hide();

				$this_form.data('doing-ajax', true);
				$this_form.find( '.st-normal' ).hide();
				$this_form.find( '.st-processing' ).show();
			},

			success: function(response, status, xhr, $this_form) {
				let $res = $this_form.find( '.response' );
				$this_form.data('doing-ajax', false);
				$this_form.find( '.st-normal' ).show();
				$this_form.find( '.st-processing' ).hide();
				$res.html("<p class='alert bg-lime-300 text-lime-950 py-2 px-4'>" + response.data.message + "</p>").show();
			},

			error: function(xhr) {
				let $res = $nl_form.find( '.response' );
				$nl_form.data('doing-ajax', false);
				$nl_form.find( '.st-normal' ).show();
				$nl_form.find( '.st-processing' ).hide();
				let error_message = 'Request can\'t be completed at the moment. Please try again later.';
				if ( xhr.hasOwnProperty( 'responseJSON' ) && xhr.responseJSON.hasOwnProperty( 'data' ) && xhr.responseJSON.data.hasOwnProperty( 'message' ) ) {
					error_message = xhr.responseJSON.data.message;
				}
				$res.html("<p class='alert bg-rose-300 text-rose-950 py-2 px-4'>"+ error_message +"</p>").show();
			},
		});
	}

	let $contact_form = $('#frm_contact');
	if ($contact_form.length) {
		$contact_form.ajaxForm({
			beforeSubmit: function(formdata, $this_form, options) {
				if ($this_form.data('doing-ajax')) {
					return false;
				}

				let $res = $this_form.find( '.response' );
				$res.html('').hide();

				let $captcha = $this_form.find('[name="g-recaptcha-response"]');
				if ($captcha.length > 0 && ! $captcha.val()) {
					$res.show();
					$res.append("<p class='alert bg-rose-300 text-rose-950 py-2 px-4'>Please complete the captcha first.</p>");
					return false;
				}

				$this_form.data('doing-ajax', true);
				$this_form.find( '.st-normal' ).hide();
				$this_form.find( '.st-processing' ).show();
			},

			success: function(response, status, xhr, $this_form) {
				let $res = $this_form.find( '.response' );
				$this_form.data('doing-ajax', false);
				$this_form.find( '.st-normal' ).show();
				$this_form.find( '.st-processing' ).hide();
				$res.html("<p class='alert bg-lime-300 text-lime-950 py-2 px-4'>" + response.data.message + "</p>").show();
			},

			error: function(xhr) {
				let $res = $contact_form.find( '.response' );
				$contact_form.data('doing-ajax', false);
				$contact_form.find( '.st-normal' ).show();
				$contact_form.find( '.st-processing' ).hide();
				let error_message = 'Request can\'t be completed at the moment. Please try again later.';
				if ( xhr.hasOwnProperty( 'responseJSON' ) && xhr.responseJSON.hasOwnProperty( 'data' ) && xhr.responseJSON.data.hasOwnProperty( 'message' ) ) {
					error_message = xhr.responseJSON.data.message;
				}
				$res.html("<p class='alert bg-rose-300 text-rose-950 py-2 px-4'>"+ error_message +"</p>").show();
			},
		});
	}
    
});

/* Progress bar */
//Source: https://alligator.io/js/progress-bar-javascript-css-variables/
var h = document.documentElement,
	b = document.body,
	st = 'scrollTop',
	sh = 'scrollHeight',
	progress = document.querySelector('#progress'),
	scroll;
var scrollpos = window.scrollY;
var header = document.getElementById("header");
var navcontent = document.getElementById("nav-content");

document.addEventListener('scroll', function() {

	/*Refresh scroll % width*/
	scroll = (h[st] || b[st]) / ((h[sh] || b[sh]) - h.clientHeight) * 100;
	progress.style.setProperty('--scroll', scroll + '%');

	/*Apply classes for slide in bar*/
	scrollpos = window.scrollY;

	if (scrollpos > 10) {
		header.classList.add("bg-white");
		header.classList.add("shadow");
		navcontent.classList.remove("bg-gray-100");
		navcontent.classList.add("bg-white");
	} else {
		header.classList.remove("bg-white");
		header.classList.remove("shadow");
		navcontent.classList.remove("bg-white");
		navcontent.classList.add("bg-gray-100");

	}

});

//Javascript to toggle the menu
document.getElementById('nav-toggle').onclick = function() {
	document.getElementById("nav-content").classList.toggle("hidden");
}