<?php


/* WordPress Responsive Menu Walker
---------------------------------------------------------------------------------------------- */
class Mobile_Menu_Walker extends Walker_Nav_Menu
{

	function start_el ( &$output, $item, $depth=0, $args=[], $id=0 )
	{
		if ( $args->walker->has_children )
		{
			$output .= "<li class='has-children cf'>";
		}
		else
		{
			$output .= "<li class=''>";
		}

		if ( $item->url && $item->url != '#' )
		{
			$output .= '<a href="' . $item->url . '">';
		}
		else
		{
			$output .= '<a href="javascript:void(0);">';
		}

		$output .= $item->title;

		if ( $item->url && $item->url != '#' )
		{
			$output .= '</a>';
		}
		else
		{
			$output .= '</a>';
		}


		if ( $args->walker->has_children )
		{
			$output .= '<div class="submenu-button"></div>';
		}
	}

}



/* Allow shortcodes
---------------------------------------------------------------------------------------------- */
add_filter( 'wp_nav_menu_items', 'do_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );



/* Mobile Menu Shortcode
---------------------------------------------------------------------------------------------- */
add_shortcode( 'mobile_menu', 'wgd_mobile_menu_shortcode' ); 
function wgd_mobile_menu_shortcode ( $atts = [] )
{ 
	$atts = shortcode_atts( [
		'menu' 	=> 'Primary Menu',
		'color' => 'black',
		'align' => 'none'
	], $atts);
	
	ob_start();
	?>
		<div class='bg-overlay hidden'></div>
		<div class='mobile-menu-parent'>
			<ul id='mobile-menu' class='hidden'>
				<?php wp_nav_menu( ['menu' => $atts['menu'],'menu_class' => 'genesis-nav-menu','walker' => new Mobile_Menu_Walker()] ); ?>
			</ul>
			<a href='javascript:void(0)' class='mobile-menu-button color-<?php echo $atts['color']; ?> align-<?php echo $atts['align']; ?>'><i class='fas fa-bars'></i></a>
		</div>
	<?php
	return ob_get_clean();
}



/* Mobile Menu CSS
---------------------------------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'wgd_mobile_menu_styles_scripts' );
function wgd_mobile_menu_styles_scripts ()
{
	wp_enqueue_style( 'wgd-mobile-menu', get_stylesheet_directory_uri() . '/wgd/wp-mobile-menu.css', [], '1.0.0' );
	wp_enqueue_script( 'wgd-mobile-menu', get_stylesheet_directory_uri() . '/wgd/wp-mobile-menu.js', ['jquery'], '1.0.0', true );

	wp_deregister_script( 'superfish' );
	wp_deregister_script( 'superfish-args' );
}