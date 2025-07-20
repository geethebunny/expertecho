<?php /* Template Name: Shortlist */

remove_action( 'genesis_loop', 'genesis_do_loop' );

# After Header
add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	$speakers = ( isset($_COOKIE['speakers']) ) ? explode( '|', $_COOKIE['speakers'] ) : ['0'] ;

	$args = [
		'post_type' 		=> 'australian-speakers',
		'posts_per_page'	=> -1,
		'order'				=> 'ASC',
		'post__in'			=> $speakers
	];

	$query = new WP_Query($args);
	?>
		<div class='shortlist-content'>
			<div class='wrap'>
				<div class='shortlist-buttons'>
					<div class='left'>
						<a href='<?php echo get_permalink(32); ?>'>
							<i class='fa-solid fa-plus'></i> Add More Speakers
						</a>
					</div>
					<div class='right'>
						<a href='<?php echo get_permalink(36); ?>'>
							<i class='fa-solid fa-phone'></i> Enquire Speakers
						</a>
					</div>
				</div>
				<div class='shortlist-list'>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php $image = get_speaker_thumbnail( get_the_ID() ); ?>

						<a href='<?php the_permalink(); ?>' class='search-speaker' style='background-image: linear-gradient(black, black)<?php if ( $image ) : ?>,url(<?php echo $image; ?>)<?php endif; ?>;' data-speaker-id='<?php the_ID(); ?>'>
							<div class='remove-button' data-url='<?php echo get_permalink(1883); ?>'>
								<i class='fa-solid fa-xmark'></i>
							</div>
							<div class='search-speaker-content'>
								<div class='name'>
									<?php echo str_replace(' ',"\n", get_the_title()); ?>
								</div>
								<div class='excerpt'>
									<?php // echo apply_filters( 'the_content', wp_trim_words( strip_tags( get_the_content() ), 8 ) ); ?>
									<?php echo get_field( 'TagLine' ); ?>
								</div>
							</div>
						</a>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	<?php
}

# Genesis
genesis();