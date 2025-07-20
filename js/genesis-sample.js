(function($) {

	// Sticky header
	if ( $(window).scrollTop() >= 1 )
		$('.site-header').addClass('sticky');

	$(window).scroll(function() {
		var scroll = $(window).scrollTop();
		if (scroll >= 1)
			$('.site-header').addClass('sticky');
		else
			$('.site-header').removeClass('sticky');
	});

})(jQuery);