(function($) {

	// Open
	$('.mobile-menu-button').on('click', function(){
		$('#mobile-menu').removeClass('hidden');
		$('.bg-overlay').removeClass('hidden');
	});

	// Close
	$('#mobile-menu').on('click', function(e){
		e.stopPropagation();
	});

	$('.bg-overlay').on('click', function(){
		$('#mobile-menu').addClass('hidden');
		$('.bg-overlay').addClass('hidden');
	});


	// Open Submenu
	$('#mobile-menu .submenu-button').on('click', function(){
		$(this).toggleClass('selected');
		$(this).siblings('.sub-menu').slideToggle();
	});
	
})(jQuery);