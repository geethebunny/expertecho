<?php

# Remove Content
remove_action( 'genesis_loop', 'genesis_do_loop' );

# Removes Title and Description on Archive, Taxonomy, Category, Tag
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );

# After Header
remove_action( 'genesis_after_header', 'theme_after_header' );
add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	$header_bg = get_field( 'hero_background', get_queried_object() );

	if ( !$header_bg )
	{
		$page = ( isset($_GET['sp']) ) ? $_GET['sp'] : 1;
		$args = [
			'post_type' 		=> 'experts',
			'posts_per_page'	=> 24,
			'paged'				=> $page,
			'order'				=> 'ASC',
		];

		# Category 
		$args['tax_query'][] = [ 
			'taxonomy'	=> 'expert-categories',
			'field'		=> 'id',
			'terms'		=> get_queried_object_id()
		];

		$bg_query = new WP_Query($args);
		$found = false;
		
		while ( $bg_query->have_posts() && !$found )
		{
			$bg_query->the_post();
			
			$images = get_field( 'speaker_images' );
			$image = $images;

			if ( is_array($images) )
			{
				$image = null;
				foreach ( $images as $data )
				{
					$size = getimagesize( speakers_image_url($data['url']) );

					if ( is_array($size) )
						if ( $size[0] == 1920 && $size[1] == 960 )
						{
							$header_bg = speakers_image_url($data['url']);
							$found = true;
						}
				}
			}
		}

		if ( !$header_bg )
			$header_bg = get_stylesheet_directory_uri() . '/images/ASB_Placeholder_3.png';
		
	}


	?>
		<div class='after-header' style='background-image:url(<?php echo $header_bg; ?>)'>
			<div class='wrap'>
				<div class='subheading'>Topic</div>
				<h1><?php echo single_term_title() ?></h1>
				<div class='description'><?php echo term_description(); ?></div>
			</div>
		</div>
		<?php
			$about_heading = get_field( 'about_heading', get_queried_object() );
			$about_text = get_field( 'about_text', get_queried_object() );
		?>
		<?php if ( $about_heading or $about_text ) : ?>
			<div class='about-section'>
				<div class='wrap'>
					<div class='left'>
						<div class='heading'><?php echo $about_heading; ?></div>
					</div>
					<div class='right'><?php echo $about_text; ?></div>
				</div>
			</div>
		<?php endif; ?>
		<div class='search-page-content'>
			<div class='left'>
				<div class='filter-row hidden'>
					<input type='checkbox' data-tier='2' value='<?php echo get_queried_object_id(); ?>' checked>
				</div>
				<div class='heading'><?php echo single_term_title(); ?></div>
				<?php
					$t2_meta = get_term_meta( get_queried_object_id() );

					$t3_categories = get_terms([
						'taxonomy' 		=> 'Tier Three',
						'orderby' 		=> 'name',
						'order'   		=> 'ASC',
						'hide_empty'	=> true
					]);

					foreach ( $t3_categories as $t3_cat )
					{
						$t3_meta = get_term_meta( $t3_cat->term_id );
						
						if ( $t3_meta['ParentID'][0] === $t2_meta['ID'][0] )
						{
							$checked = '';
							if ( isset($_GET['categories']) )
								$checked = ($_GET['categories'] == $t2_cat->term_id) ? 'checked' : '' ;
							echo '<div class="filter-row speakers-filter-row">';
							echo "<input type='checkbox' data-tier='3' name='$t3_cat->slug' value='$t3_cat->term_id' $checked> $t3_cat->name";
							echo '</div>';
						}
					}
				?>
			</div>
			<div class='tablet-only mobile-toggle'>
				<i class='fa-solid fa-chevron-down'></i>
				<i class='fa-solid fa-chevron-up'></i>
			</div>
			<div class='right'>
				<div class='main-results'>
					<?php
						$page = ( isset($_GET['sp']) ) ? $_GET['sp'] : 1;
						$args = [
							'post_type' 		=> 'australian-speakers',
							'posts_per_page'	=> 24,
							'paged'				=> $page,
							'order'				=> 'ASC',
						];
					
						# Search
						if ( isset($_GET['search']) )
							$args['s'] = $_GET['search'];
					
						# Category 
						$args['tax_query'][] = [ 
							'taxonomy'	=> 'Tier Two',
							'field'		=> 'id',
							'terms'		=> get_queried_object_id()
						];
					
						$query = new WP_Query($args);
					?>
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php $image = get_speaker_thumbnail( get_the_ID() ); ?>

						<a href='<?php the_permalink(); ?>' class='search-speaker' style='background-image: linear-gradient(black, black)<?php if ( $image ) : ?>,url(<?php echo $image; ?>)<?php endif; ?>;'>
							<div class='search-speaker-content'>
								<div class='name'>
									<?php echo str_replace(' ',"\n", get_the_title()); ?>
								</div>
								<div class='excerpt'>
									<?php echo apply_filters( 'the_content', wp_trim_words( strip_tags( get_the_content() ), 8 ) ); ?>
								</div>
							</div>
						</a>
					<?php endwhile; ?>
				</div>
				<div class='spinner hidden'>
					<i class='fas fa-circle-notch fa-spin'></i>
				</div>
				<div class='empty-results hidden'>
					No Results.
				</div>
				<div class='pagination speakers-pagination'>
					<?php
						$page = ( isset($_GET['sp']) ) ? $_GET['sp'] : 1 ;
						echo paginate_links( array(
							'base'         => '%_%',
							'total'        => $query->max_num_pages,
							'current'      => max( 1, $page ),
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
						) );
					?>
				</div>
			</div>
		</div>
	<?php
}


# Genesis
genesis();