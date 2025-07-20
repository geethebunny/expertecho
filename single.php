<?php

remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

# After Header
remove_action( 'genesis_after_header', 'theme_after_header' );
add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	global $post;
	$categories = get_the_category();
	?>
		<div class='after-header'>
			<div class='wrap'>
				<div class='categories'>
					<?php foreach ( $categories as $key => $category ) : ?>
						<?php if ( $key != 0 ) echo ' / '; ?>
						<?php echo $category->name ?>
					<?php endforeach; ?>
				</div>
				<h1><?php the_title(); ?></h1>
				<div class='author-area'>
					<div class='left'>
						<div class='image' style='background-image:url(<?php echo get_avatar_url($post->post_author); ?>);'></div>
					</div>
					<div class='right'>
						<div class='name'><?php echo get_the_author_meta('display_name', $post->post_author); ?></div>
						<div class='date'><?php echo get_the_date(); ?></div>
					</div>
				</div>
			</div>
		</div>
	<?php
}

genesis();