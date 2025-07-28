<?php

/**
 * Functions
 *
 * @package      WGD Genesis Child Theme
 * @author       WGD
 * @since        1.0.0
 * @license      GPL-2.0+
 **/

// * Surpress Genesis error
add_filter('doing_it_wrong_trigger_error', '__return_false');

// * Starts the engine.
require_once get_template_directory() . '/lib/init.php';



/**
 * Styles and scripts goes here.
 *
 * @since  1.0.0
 */
function child_theme_enqueues()
{
	# CSS
	wp_dequeue_style('child-theme');
	wp_enqueue_style('theme-style', get_stylesheet_directory_uri() . '/assets/css/main.scss', [], filemtime(get_stylesheet_directory() . '/assets/css/main.scss'));
	wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap', [], null);

	# JavaScript
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//code.jquery.com/jquery-3.7.1.min.js', false, '3.7.1', true);
	wp_enqueue_script('theme-global', get_stylesheet_directory_uri() . '/assets/js/global.js', ['jquery', 'lucide'], filemtime(get_stylesheet_directory() . '/assets/js/global.js'), true);

	# Flickity
	wp_enqueue_style('flickity', '//cdnjs.cloudflare.com/ajax/libs/flickity/3.0.0/flickity.min.css', [], '3.0.0');
	wp_enqueue_script('flickity', '//cdnjs.cloudflare.com/ajax/libs/flickity/3.0.0/flickity.pkgd.min.js', ['jquery'], '3.0.0', true);

	# Lucide
	wp_enqueue_script('lucide', '//unpkg.com/lucide@latest', [], '1.0.0');

	# JSCookie
	wp_enqueue_script('js-cookie', '//cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js', [], '3.0.1');

	# Superfish and HoverIntent
	wp_dequeue_script('superfish');
	wp_dequeue_script('superfish-args');
	wp_dequeue_script('hoverIntent');

	# Localize variables
	wp_localize_script('theme-global', 'local_scripts', [
		'ajaxurl' => admin_url('admin-ajax.php')
	]);
}
add_action('wp_enqueue_scripts', 'child_theme_enqueues');




// * Webgee Designs
require_once get_stylesheet_directory() . '/wgd/wp-mobile-menu.php';

// * Helpers
require_once get_stylesheet_directory() . '/helpers.php';



/**
 * Theme setup.
 *
 * Attach all of the site-wide functions to the correct hooks and filters. All
 * the functions themselves are defined below this setup function.
 *
 * @since 1.0.0
 */
function child_theme_setup()
{
	define('CHILD_THEME_VERSION', filemtime(get_stylesheet_directory() . '/assets/css/main.css'));

	# Theme Support
	add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
	add_theme_support('genesis-responsive-viewport');
	add_theme_support('genesis-structural-wraps', ['header', 'site-inner', 'footer-widgets', 'footer']);
	add_theme_support('genesis-footer-widgets', 3);
	add_theme_support('genesis-accessibility', ['404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'screen-reader-text']);

	# Unregister layouts
	genesis_unregister_layout('content-sidebar-sidebar');
	genesis_unregister_layout('sidebar-content-sidebar');
	genesis_unregister_layout('sidebar-sidebar-content');

	# Unregister Sidebars
	unregister_sidebar('sidebar-alt');

	# Gutenberg
	add_theme_support('responsive-embeds');
	add_theme_support('align-wide');
}
add_action('after_setup_theme', 'child_theme_setup', 15);



/**
 * Theme meta.
 *
 * All add_actions and remove_actions should be here.
 *
 * @since 1.0.0
 */
function child_theme_meta()
{
	# Remove Emoji inline CSS
	remove_action('wp_print_styles', 'print_emoji_styles');

	# Don't enqueue child theme stylesheet
	remove_action('genesis_meta', 'genesis_load_stylesheet');

	# Theme Support: Custom Logo
	add_action('genesis_site_title', 'the_custom_logo', 0);
	add_theme_support('custom-logo', [
		'width' => 180,
		'height' => 50,
		'flex-height' => true,
		'flex-width'  => true,
	]);

	# Reposition Primary Nav
	remove_action('genesis_after_header', 'genesis_do_nav');
	add_action('genesis_header', 'genesis_do_nav', 12);

	# Remove post titles
	remove_action('genesis_entry_header', 'genesis_do_post_title');
}
add_action('genesis_meta', 'child_theme_meta', 15);



/**
 * Theme sidebars.
 *
 * Registers all sidebars for the theme.
 *
 * @since 1.0.0
 */
function child_theme_sidebars()
{
	genesis_register_sidebar([
		'id' 	=> 'footer-left',
		'name' => 'Footer - Left',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	]);

	genesis_register_sidebar([
		'id' 	=> 'footer-right',
		'name' => 'Footer - Right',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	]);
}
add_action('widgets_init', 'child_theme_sidebars');



/**
 * Excerpt.
 *
 * Sets the excerpt length.
 *
 * @since 1.0.0
 */
function child_theme_excerpt($length)
{
	return 20;
}
add_filter('excerpt_length', 'child_theme_excerpt');



# Footer
remove_action('genesis_footer', 'genesis_footer_markup_open', 5);
remove_action('genesis_footer', 'genesis_do_footer');
remove_action('genesis_footer', 'genesis_footer_markup_close', 15);
add_action('genesis_footer', 'child_theme_footer');
function child_theme_footer()
{
	$experts = (isset($_COOKIE['experts'])) ? explode('|', $_COOKIE['experts']) : [];
?>
	<div class='toast-container'></div>
	<div class='shortlist'>
		<a href='<?php echo get_permalink(1883); ?>'>
			<i data-lucide='mic-vocal' class='icon'></i>
			<span class='shortlist-count'><?php echo count(array_filter($experts)); ?></span>
		</a>
	</div>
	<section class='footer-cta'>
		<div class='wrap'>
			<h4>Stay in the Loop</h4>
			<p>Get updates and news from Expert Echo</p>
			<form onsubmit='return false;'>
				<div class='cols'>
					<div class='col col--left'>
						<input type='email' name='ne' placeholder='you@example.com'>
					</div>
					<div class='col col--middle'>
						<input type='text' name='nn' placeholder='Jane Doe'>
					</div>
					<div class='col col--right'>
						<input type='submit' value='Join Now' class='button'>
					</div>
				</div>
			</form>
		</div>
	</section>
	<footer class='site-footer'>
		<div class='wrap cols'>
			<?php
			genesis_widget_area('footer-left', [
				'before' => '<div class="col col--left widget-area">',
				'after'  => '</div>',
			]);
			?>
			<?php
			genesis_widget_area('footer-right', [
				'before' => '<div class="col col--right widget-area">',
				'after'  => '</div>',
			]);
			?>
		</div>
	</footer>
<?php
}



# Shortcodes
add_filter('wp_nav_menu_items', 'do_shortcode');
add_shortcode('expert-search', 'expert_search_shortcode');
function expert_search_shortcode()
{
	ob_start();

?>
	<form action='<?php echo get_site_url(); ?>/experts/'>
		<input type='search' placeholder='Find an Expert' name='search' class='header-search-input'>
		<i data-lucide='search' class='icon icon--search'></i>
		<div class='header-search hidden'>
			<div class='spinner'>
				<i data-lucide='loader-circle' class='icon icon--spinner'></i>
			</div>
			<div class='empty-results hidden'>No results.</div>
			<div class='results'></div>
		</div>
	</form>
<?php

	return ob_get_clean();
}


# After Header
add_action('genesis_after_header', 'theme_after_header');
function theme_after_header()
{
	$page_header_banner = get_field('page_header_banner');
	$page_header_subheading = get_field('page_header_subheading');
?>
	<div class='after-header' style='background-image: url(<?php echo $page_header_banner; ?>);'>
		<div class='wrap'>
			<h1><?php the_title(); ?></h1>
			<?php if ($page_header_subheading) : ?>
				<div class='tagline'><?php echo $page_header_subheading; ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}



# AJAX: Header Search
add_action('wp_ajax_nopriv_header_search', 'ajax_header_search');
add_action('wp_ajax_header_search', 'ajax_header_search');
function ajax_header_search()
{
	ob_start();

	# Keywords
	$categories = get_terms([
		'taxonomy' 			=> 'expert-categories',
		'orderby' 			=> 'name',
		'order'   			=> 'ASC',
		'hide_empty'		=> true,
		'name__like'    	=> $_GET['s_name']
	]);

	if ($categories) {
		foreach ($categories as $category) {
	?>
			<div class='expert'>
				<a href='<?php echo get_term_link($category); ?>'>
					<?php echo $category->name; ?>
				</a>
			</div>
		<?php
		}
	}

	# Experts
	$args = [
		'post_type' 		=> 'experts',
		'posts_per_page'	=> -1,
		'order'				=> 'ASC',
		's_title'			=> $_GET['s_name']
	];

	add_filter('posts_where', 'title_filter', 10, 2);
	$query = new WP_Query($args);
	remove_filter('posts_where', 'title_filter', 10, 2);

	while ($query->have_posts()) : $query->the_post();

		?>
		<div class='expert' data-expert-id='<?php the_ID(); ?>' data-expert-name='<?php the_title(); ?>'>
			<a href='<?php echo the_permalink(); ?>'>
				<div class='name'><?php the_title(); ?></div>
			</a>
		</div>
	<?php
	endwhile;

	wp_send_json(ob_get_clean());
	exit;
}




# AJAX: Expert Search Prediction
add_action('wp_ajax_nopriv_expert_spearch_prediction', 'ajax_expert_search_prediction');
add_action('wp_ajax_expert_spearch_prediction', 'ajax_expert_search_prediction');
function ajax_expert_search_prediction()
{
	$args = [
		'post_type' 		=> 'experts',
		'posts_per_page'	=> -1,
		'order'				=> 'ASC',
		's_title'			=> $_GET['s_name']
	];

	add_filter('posts_where', 'title_filter', 10, 2);
	$query = new WP_Query($args);
	remove_filter('posts_where', 'title_filter', 10, 2);

	$cookie_experts = explode('|', $_COOKIE['experts']);

	ob_start();

	while ($query->have_posts()) : $query->the_post();

		if (in_array(get_the_ID(), $cookie_experts))
			continue;

		$image = get_expert_thumbnail(get_the_ID());
		$categories = wp_get_post_terms(get_the_ID(), 'Tier Two');

	?>
		<div class='enquire-expert' data-expert-id='<?php the_ID(); ?>' data-expert-name='<?php the_title(); ?>'>
			<div class='left'>
				<div class='image' style='background-image: url(<?php echo $image; ?>)'></div>
			</div>
			<div class='right'>
				<div class='name'><?php the_title(); ?></div>
				<div class='categories'>
					<?php
					$category_string = '';
					foreach ($categories as $category) {
						$t2_meta = get_term_meta($category->term_id);

						if ($t2_meta['ParentID'][0] == 101) {
							$category_string .= $category->name . ' / ';
						}
					}

					echo substr($category_string, 0, -3);
					?>
				</div>
			</div>
			<div class='close'>
				<i data-lucide='x' class='icon icon--x'></i>
			</div>
		</div>
	<?php
	endwhile;

	wp_send_json(ob_get_clean());
	exit;
}



# Title Search Filter
function title_filter($where, &$wp_query)
{
	global $wpdb;
	if ($search_term = $wp_query->get('s_title')) {
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql(like_escape($search_term)) . '%\'';
	}
	return $where;
}



# AJAX: Main Search Experts
add_action('wp_ajax_nopriv_main_search_experts', 'ajax_main_search_experts');
add_action('wp_ajax_main_search_experts', 'ajax_main_search_experts');
function ajax_main_search_experts()
{
	$shortlist_experts = (isset($_COOKIE['experts'])) ? explode('|', $_COOKIE['experts']) : [];

	$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
	$args = [
		'post_type' 		=> 'experts',
		'posts_per_page'	=> 24,
		'orderby'			=> ['title' => 'ASC'],
		's'					=> $_GET['search'],
		'paged'				=> $page
	];

	if ($_GET['categories']) {
		$categories = explode('|', $_GET['categories']);
		$args['tax_query']['relation'] = 'OR';

		foreach ($categories as $category) {
			$args['tax_query'][] = [
				'taxonomy'	=> 'expert-categories',
				'field'		=> 'id',
				'terms'		=> $category
			];
		}
	}

	$query = new WP_Query($args);

	# Experts
	ob_start();
	while ($query->have_posts()) : $query->the_post();
		$image = get_expert_thumbnail(get_the_ID());

	?>
		<a href='<?php the_permalink(); ?>' class='search-expert' style='background-image: linear-gradient(black, black)<?php if ($image) : ?>,url(<?php echo $image; ?>)<?php endif; ?>;' data-expert-id='<?php echo get_the_ID(); ?>'>
			<div class='add-button <?php if (in_array(get_the_ID(), $shortlist_experts)) echo 'active'; ?>' data-url='<?php echo get_permalink(1883); ?>'>
				<i data-lucide='plus' class='icon icon--plus <?php if (in_array(get_the_ID(), $shortlist_experts)) echo 'hidden'; ?>'></i>
				<i data-lucide='x' class='icon icon--x <?php if (!in_array(get_the_ID(), $shortlist_experts)) echo 'hidden'; ?>'></i>
			</div>
			<div class='search-expert-content'>
				<div class='name'>
					<?php echo str_replace(' ', "\n", get_the_title()); ?>
				</div>
				<div class='excerpt'>
					<?php // echo apply_filters( 'the_content', wp_trim_words( strip_tags( get_the_content() ), 8 ) ); 
					?>
					<?php echo get_field('tagline'); ?>
				</div>
			</div>
		</a>
	<?php
	endwhile;
	$return['experts'] = ob_get_clean();

	# Pagination
	$return['pagination'] = paginate_links(array(
		'base'         => '%_%',
		'total'        => $query->max_num_pages,
		'current'      => max(1, $page),
		'format'       => '?sp=%#%',
		'show_all'     => false,
		'type'         => 'plain',
		'end_size'     => 2,
		'mid_size'     => 1,
		'prev_next'    => true,
		'prev_text'    => '<',
		'next_text'    => '>',
		'add_args'     => false,
		'add_fragment' => '',
	));

	wp_send_json($return);
	exit;
}



# AJAX: Blog
add_action('wp_ajax_nopriv_blog_search_experts', 'ajax_search_blog');
add_action('wp_ajax_blog_search_experts', 'ajax_search_blog');
function ajax_search_blog()
{
	$args = [
		'post_type' 		=> 'post',
		'posts_per_page'	=> -1,
		'order'				=> 'DESC',
		's'					=> $_GET['search']
	];

	if ($_GET['categories']) {
		$categories = explode('|', $_GET['categories']);
		$args['cat'] = $categories;
	}

	$query = new WP_Query($args);

	ob_start();

	while ($query->have_posts()) : $query->the_post();
		$image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'single-post-thumbnail');
	?>
		<a href='<?php the_permalink(); ?>' class='search-expert' style='background-image: linear-gradient(black, black)<?php if ($image) : ?>,url(<?php echo $image[0]; ?>)<?php endif; ?>;'>
			<div class='search-expert-content'>
				<div class='name'>
					<?php echo get_the_title(); ?>
				</div>
				<div class='author'>
					<?php the_author(); ?>
				</div>
				<div class='date'>
					<?php echo get_the_date(); ?>
				</div>
			</div>
		</a>
<?php
	endwhile;

	wp_send_json(ob_get_clean());
	exit;
}





add_action('rest_api_init', function () {
	register_rest_route('api/v1', '/shortlist_add', array(
		'methods' => 'POST',
		'callback' => 'shortlist_add'
	));
});

function shortlist_add($req)
{
	$return = isset($req['return']) ? $req['return'] : get_permalink(1883);

	if (isset($_COOKIE['experts'])) {
		$experts = explode('|', $_COOKIE['experts']);

		if (!in_array($req['expert-id'], $experts)) {
			$experts[] = $req['experts-id'];
		}

		setcookie('experts', implode($experts, '|'), 0, '/');
	} else {
		setcookie('experts', $req['expert-id'], 0, '/');
	}

	header('Location: ' . $return);
	die();
}
