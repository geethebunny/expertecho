(function($) {

	$(document).ready(function () {

		// Header Search START
		var header_search_timer;
		$('.header-search-input').on('input', function(e){
			if ( $(this).val().length >= 3 )
			{
				clearTimeout( header_search_timer );
				header_search_timer = setTimeout( header_search_finish, 1000 );				
			}
			else
			{
				$('.header-search').addClass('hidden');
			}
		});

		function header_search_finish ()
		{
			var s_name = '';
			$('.header-search-input').each( function(i){
				s_name = s_name + $(this).val();
			});
			$.ajax({
				url: local_scripts.ajaxurl,
				type: 'get',
				data: {
					'action': 'header_search',
					's_name': s_name,
				},
				dataType: 'json',
				beforeSend: function () {
					// Show
					$('.header-search').removeClass('hidden');

					// Show spinner
					$('.header-search .spinner').removeClass('hidden');

					// Clear list
					$('.header-search .results').empty();

					// Hide no results
					$('.header-search .empty-results').addClass('hidden');
				},
				complete: function () {
					// Hide spinner
					$('.header-search .spinner').addClass('hidden');
				},
				success: function (data,status,xhr) {

					if ( data != "" )
						$('.header-search .results').append( data );
					else
						$('.header-search .empty-results').removeClass('hidden');
				},
				error: function (jqXhr, textStatus, errorMessage) {
				}
			});
		}
		// Header Search END

		// Enquire Search START
		var speaker_search_predict_timer;

		$('#speakers-text').on('input', function(e){
			if ( $(this).val().length >= 3 )
			{
				clearTimeout( speaker_search_predict_timer );
				speaker_search_predict_timer = setTimeout( speaker_search_predict_finish, 1000 );				
			}
			else
			{
				$('#enquire-speakers-search').addClass('hidden');
			}
		});

		function speaker_search_predict_finish ()
		{
			$.ajax({
				url: local_scripts.ajaxurl,
				type: 'get',
				data: {
					'action': 'speaker_spearch_prediction',
					's_name': $('#speakers-text').val(),
				},
				dataType: 'json',
				beforeSend: function () {
					// Show
					$('#enquire-speakers-search').removeClass('hidden');

					// Show spinner
					$('#enquire-speakers-search .spinner').removeClass('hidden');

					// Clear list
					$('#enquire-speakers-prediction').empty();

					// Hide no results
					$('#enquire-speakers-search .empty-results').addClass('hidden');
				},
				complete: function () {
					// Hide spinner
					$('#enquire-speakers-search .spinner').addClass('hidden');
				},
				success: function (data,status,xhr) {

					if ( data != "" )
						$('#enquire-speakers-prediction').append( data );
					else
						$('#enquire-speakers-search .empty-results').removeClass('hidden');
				},
				error: function (jqXhr, textStatus, errorMessage) {
				}
			});
		}


		// Speaker Enquiry Click
		$('#enquire-speakers-prediction').on('click', '.enquire-speaker', function(){

			var id = String($(this).data('speaker-id'));
			var cookie_speakers = Cookies.get('speakers');

			// Speakers exists
			if ( cookie_speakers )
			{
				cookie_speakers = cookie_speakers.split('|');

				// Check if already exists
				if ( !cookie_speakers.includes(id))
					cookie_speakers.push( id );
				else
					return;
			}
			else
			{
				cookie_speakers = [ id ];
			}

			// Join
			cookie_speakers = cookie_speakers.join('|');

			Cookies.set('speakers', cookie_speakers);


			// Remove input
			$('#speakers-text').val('');

			// Update hidden value
			if ( $('#speakers-hiddenlist').val() )
				$('#speakers-hiddenlist').val( $('#speakers-hiddenlist').val() + '\r\n' + $(this).data('speaker-name') );
			else
				$('#speakers-hiddenlist').val( $(this).data('speaker-name') );

			// Hide search
			$('#enquire-speakers-search').addClass('hidden');

			// Append
			$('#enquire-speakers-list').append( $(this) );
		});


		// Speaker Enquiry Remove
		$('#enquire-speakers-list').on('click', 'i', function(){
			
			// Init
			var id = String($(this).parent().parent().data('speaker-id'));
			var name = $(this).parent().parent().data('speaker-name');
			var cookie_speakers = Cookies.get('speakers').split('|');
			var speakers_hidden_list = $('#speakers-hiddenlist').val();

			// Check if not last element
			if ( speakers_hidden_list.includes(', ') )
			{
				// Update hidden value
				$('#speakers-hiddenlist').val( speakers_hidden_list.replace(name + ', ', '').replace(', ' + name, '') );

				// Update cookies
				cookie_speakers = cookie_speakers.filter(function(e) { return e !== id })

				Cookies.set( 'speakers', cookie_speakers.join('|') );
			}
			else
			{
				// Remove hidden value
				$('#speakers-hiddenlist').val('');
				
				// Remove cookies
				Cookies.remove( 'speakers' );
			}

			// Remove element
			$(this).parent().parent().slideUp();
		});


		// Page - Speaker Enquiry
		if ( $('body').hasClass('page-template-template_enquiries') )
		{
			// Load speakers
			$('#enquire-speakers-list').append( $('#cookie-speakers .enquire-speaker') );

			// Set form value
			$('#speakers-hiddenlist').val( $('#cookie-speakers-form-input').data('value') );
		}


		// Prevent Form submit
		$('.form-no-submit').on('submit', function(e){
			e.preventDefault();
		})
		


		var main_search_timer;
		var current_page = 1;

		$('#main-search').on('input', function(e){
			var val = $(this).val();

			clearTimeout( main_search_timer );
			main_search_timer = setTimeout( main_search_timer_finish, 1000 );
		});

		// Change Category
		$('.speakers-filter-row input[type=checkbox]').on('change', function(e){

			var child = $(this).parent().next('.filter-row-child');

			// If parent and closed, deselect all child
			if ( child )
			{
				if ( $(this).prop('checked') )
				{
					child.removeClass('hidden');
				}
				else
				{
					child.find('input[type=checkbox]').prop('checked', false);
					child.addClass('hidden');
				}
			}

			clearTimeout( main_search_timer );
			// main_search_timer = setTimeout( main_search_timer_finish, 1000 );
		});

		// Pagination
		$('.speakers-pagination').on('click', 'a', function(e) {
			e.preventDefault();

			console.log( $(this).html() );

			// Set Page
			if ( $(this).html() == '&lt;' )
				current_page--;
			else if ( $(this).html() == '&gt;' )
				current_page++;
			else
				current_page = $(this).html();

			// Query
			clearTimeout( main_search_timer );
			main_search_timer = setTimeout( main_search_timer_finish, 0 );

			// Scroll
			$([document.documentElement, document.body]).animate({
				scrollTop: $('.main-results').offset().top - 150
			}, 50);
		});

		function main_search_timer_finish ()
		{
			var url = window.location.href.split('?')[0];
			var t2_boxes = [];
			var t3_boxes = [];

			$('.filter-row input[data-tier="2"]:checked').each(function() {
				t2_boxes.push($(this).val());
			});

			
			$('.filter-row input[data-tier="3"]:checked').each(function() {
				t3_boxes.push($(this).val());
			});

			$.ajax({
				url: local_scripts.ajaxurl,
				type: 'get',
				data: {
					'action': 'main_search_speakers',
					'search': $('#main-search').val(),
					't2_categories': t2_boxes.join('|'),
					't3_categories': t3_boxes.join('|'),
					'page': current_page
				},
				dataType: 'json',
				beforeSend: function () {
					// Show spinner
					$('.search-page-content .spinner').removeClass('hidden');

					// Clear list
					$('.search-page-content .main-results').empty();

					// Clear Paginatiion
					current_page = 1;
					$('.search-page-content .pagination').empty();

					// Hide no results
					$('.search-page-content .empty-results').addClass('hidden');

					// Clear URL
					window.history.pushState( 'Speakers Search', 'Speakers', url );
				},
				complete: function () {
					// Hide spinner
					$('.search-page-content .spinner').addClass('hidden');
				},
				success: function (data,status,xhr) {

					if ( data != '' )
					{
						$('.search-page-content .main-results').append( data.speakers );
						$('.search-page-content .pagination').append( data.pagination );
					}
					else
					{
						$('.search-page-content .empty-results').removeClass('hidden');
					}
				},
				error: function (jqXhr, textStatus, errorMessage) {
				}
			});
		}



		// Blog Search
		var blog_search_timer;

		$('#blog-search').on('input', function(e){
			clearTimeout( blog_search_timer );
			blog_search_timer = setTimeout( blog_search_timer_finish, 1000 );
		});

		// Category
		$('body.page-template-template-blog .filter-row input[type=checkbox]').on('change', function(e){
			clearTimeout( blog_search_timer );
			blog_search_timer = setTimeout( blog_search_timer_finish, 1000 );
		});

		function blog_search_timer_finish ()
		{
			var category_boxes = [];

			$('.filter-row input[type=checkbox]:checked').each(function() {
				category_boxes.push($(this).val());
			});

			$.ajax({
				url: local_scripts.ajaxurl,
				type: 'get',
				data: {
					'action': 'blog_search_speakers',
					'search': $('#blog-search').val(),
					'categories': category_boxes.join('|')
				},
				dataType: 'json',
				beforeSend: function () {
					// Show spinner
					$('.search-page-content .spinner').removeClass('hidden');

					// Clear list
					$('.search-page-content .main-results').empty();

					// Hide no results
					$('.search-page-content .empty-results').addClass('hidden');
				},
				complete: function () {
					// Hide spinner
					$('.search-page-content .spinner').addClass('hidden');
				},
				success: function (data,status,xhr) {

					if ( data != "" )
						$('.search-page-content .main-results').append( data );
					else
						$('.search-page-content .empty-results').removeClass('hidden');
				},
				error: function (jqXhr, textStatus, errorMessage) {
				}
			});
		}



		// Toggle Filters
		$('.mobile-toggle').on('click', function (e){

			if ( $(this).hasClass('active') )
			{
				$(this).siblings('.left').slideUp();
			}
			else
			{
				$(this).siblings('.left').slideDown();
			}
			$(this).toggleClass('active');
		});



		// Flickity
		$('.speaker-media-list').flickity({
			// options
			contain: true,
			pageDots: false,
			adaptiiveHeight: true,
			wrapAround: true,
			watchCSS: true
		});


		// Shortlist Add
		$('.main-results').on('click', '.add-button', function(e){
			e.preventDefault();

			// Init
			var id = String($(this).parent().data('speaker-id'));
			var cookie_speakers = Cookies.get('speakers');

			if ( cookie_speakers )
				cookie_speakers = cookie_speakers.split('|');

			// Update cookies
			if ( $(this).children('.fa-times').hasClass('hidden') )
			{
				if ( Array.isArray(cookie_speakers) )
					cookie_speakers = cookie_speakers.filter(function(e) { return e !== id });
				else
					cookie_speakers = [];
					
				cookie_speakers.push( id );

				Cookies.set( 'speakers', cookie_speakers.join('|') );

				$(this).find('.fa-plus').addClass('hidden');
				$(this).find('.fa-times').removeClass('hidden');

				// Update text
				$('.shortlist-footer .number').html( parseInt($('.shortlist-footer .number').html()) + 1 );
			}
			else
			{
				// Check if not last element
				if ( cookie_speakers.length > 1 )
				{
					// Update cookies
					cookie_speakers = cookie_speakers.filter(function(e) { return e !== id })

					Cookies.set( 'speakers', cookie_speakers.join('|') );
				}
				else
				{
					// Remove cookies
					Cookies.remove( 'speakers' );
				}

				$(this).find('.fa-plus').removeClass('hidden');
				$(this).find('.fa-times').addClass('hidden');

				// Update text
				$('.shortlist-footer .number').html( parseInt($('.shortlist-footer .number').html()) - 1 );
			}

			
		});

		// Shortlist Remove
		$('.shortlist-list .remove-button').on('click', function(e){
			e.preventDefault();
			
			// Init
			var id = String($(this).parent().data('speaker-id'));
			var cookie_speakers = Cookies.get('speakers').split('|');

			// Check if not last element
			if ( cookie_speakers.length > 1 )
			{
				// Update cookies
				cookie_speakers = cookie_speakers.filter(function(e) { return e !== id })

				Cookies.set( 'speakers', cookie_speakers.join('|') );
			}
			else
			{
				// Remove cookies
				Cookies.remove( 'speakers' );
			}

			// Reload Page
			window.location.href = $(this).data('url');

		});


		// Speaker Talking Point Toggle
		$('.talking-point .title').on('click', function(e){
			
			if ( $(this).hasClass('active') )
			{
				// $(this).find('.fa-chevron-up')
			}

			$(this).find('.fa-chevron-up').toggleClass('hidden');
			$(this).find('.fa-chevron-down').toggleClass('hidden');
			$(this).siblings('.text').toggleClass('hidden');

			$(this).toggleClass('active');
		});
	});

})(jQuery);