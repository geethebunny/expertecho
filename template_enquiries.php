<?php /* Template Name: Enquiries */


remove_action( 'genesis_loop', 'genesis_do_loop' );

add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	?>
		<div class='enquire-section'>
			<div class='wrap'>
				<div class='left'>
					<img src='<?php echo get_stylesheet_directory_uri(); ?>/images/enquire-squares.png'>
				</div>
				<div class='right'>
					<div class='heading'>
						<?php the_content(); ?>
					</div>
					<?php echo do_shortcode(' [contact-form-7 id="46"] '); ?>
				</div>
			</div>
		</div>
		<div id='cookie-speakers' class='hidden'>
			<?php
				$cookie_speakers = explode( '|', $_COOKIE['speakers'] );
				$form_input = '';

				$args = [
					'post_type' 		=> 'australian-speakers',
					'posts_per_page'	=> -1,
					'order'				=> 'ASC',
					'post__in'			=> $cookie_speakers
				];
			
				$query = new WP_Query($args);

				while ( $query->have_posts() ) : $query->the_post();
					$image = get_speaker_thumbnail( get_the_ID() );
					$categories = wp_get_post_terms( get_the_ID(), 'Tier Two' );
					$form_input .= get_the_title() . ', ';
					?>
					<div class='enquire-speaker' data-speaker-id='<?php the_ID(); ?>' data-speaker-name='<?php the_title(); ?>'>
						<div class='left'>
							<div class='image' style='background-image: url(<?php echo $image; ?>)'></div>
						</div>
						<div class='right'>
							<div class='name'><?php the_title(); ?></div>
							<div class='categories'>
								<?php
									$category_string = '';
									foreach ( $categories as $category )
									{
										$t2_meta = get_term_meta( $category->term_id );
										
										if ( $t2_meta['ParentID'][0] == 101 )
										{
											$category_string .= $category->name . ' / ';
										}
									}
			
									echo substr($category_string, 0, -3);
								?>
							</div>
						</div>
						<div class='close'>
							<i class='fa-solid fa-xmark'></i>
						</div>
					</div>
				<?php endwhile; ?>
				
		</div>
		<div id='cookie-speakers-form-input' data-value='<?php echo substr($form_input, 0, -2); ?>'></div>
	<?php
}


genesis();