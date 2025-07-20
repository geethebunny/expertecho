<?php

// To get speaker bio data, get_field('Speaker) to get the ID then get that post ID's meta, UNSERIALIZE TWICE

# Remove Content
remove_action( 'genesis_loop', 'genesis_do_loop' );

# After Header
remove_action( 'genesis_after_header', 'theme_after_header' );
add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	$speaker_meta = get_post_meta( get_field('Speaker') );

	// $images = unserialize( get_post_meta( get_the_ID(), 'images', true ) );
	$images = get_field( 'speaker_images' );
	$categories = wp_get_post_terms( get_the_ID(), 'Tier Three' );
	$image = $images;

	if ( is_array($images) )
	{
		$image = null;
		foreach ( $images as $data )
		{
			$size = @getimagesize( speakers_image_url($data['url']) );

			if ( is_array($size) )
				if ( $size[0] == 1920 && $size[1] == 960 )
					$image = speakers_image_url($data['url']);
		}

		if ( !$image )
			$image = get_stylesheet_directory_uri() . '/images/ASB_Placeholder_3.png';
	}
	?>
		<div class='after-header' style='background-image: url(<?php echo $image; ?>);'>
			<div class='wrap'>
				<h1><?php the_title(); ?></h1>
				<div class='tagline'><?php echo get_field( 'TagLine' ); ?></div>
			</div>
		</div>
		<?php 
			
		?>
		<div class='speaker-basic-info'>
			<div class='wrap'>
				<h3>Topic</h3>
				<div class='heading'>
					<?php
						$t2_categories = get_terms([
							'taxonomy' 		=> 'Tier Two',
							'orderby' 		=> 'name',
							'order'   		=> 'ASC',
							'hide_empty'	=> true
						]);

						$topic_children = [];
						foreach ( $t2_categories as $t2_category )
						{
							$t2_meta = get_term_meta( $t2_category->term_id );

							if ( $t2_meta['ParentID'][0] == 101 )
								$topic_children[] = $t2_meta['ID'][0];
						}

						$category_string = '';
						foreach ( $categories as $category )
						{
							$t3_meta = get_term_meta( $category->term_id );

							
							if ( in_array( $t3_meta['ParentID'][0], $topic_children) )
							{
								$category_string .= '<a href="' . get_category_link($category->term_id). '">' . $category->name . '</a> / ';
							}
						}

						echo substr($category_string, 0, -3);
					?>
				</div>

				<div class='speaker-cta'>
					<div class='left'>
						<form id='speaker-cta-add' action='<?php echo get_site_url(); ?>/wp-json/api/v1/shortlist_add/' method='POST'>
							<input type='hidden' name='type' value='add'>
							<input type='hidden' name='speaker-id' value='<?php echo get_the_ID(); ?>'>
							<a href='javascript:void(0);' onclick='document.getElementById("speaker-cta-add").submit(); return false'>
								<i class='fa fa-plus'></i> Add to Speaker Enquiry
							</a>
						</form>
					</div>
					<div class='right'>
						<form id='speaker-cta-send' action='<?php echo get_site_url(); ?>/wp-json/api/v1/shortlist_add/' method='POST'>
							<input type='hidden' name='type' value='add'>
							<input type='hidden' name='speaker-id' value='<?php echo get_the_ID(); ?>'>
							<input type='hidden' name='return' value='<?php echo get_permalink(36); ?>'>
							<a href='javascript:void(0);' onclick='document.getElementById("speaker-cta-send").submit(); return false'>
								<i class='far fa-envelope'></i> Send Enquiry
							</a>
						</form>
					</div>
				</div>

				<!--
				<div class='social-media-stats'>
					<div class='column'>
						<div class='number'>30</div>
						<div class='platform'>Twitter</div>
					</div>
					<div class='column'>
						<div class='number'>30</div>
						<div class='platform'>Facebook</div>
					</div>
					<div class='column'>
						<div class='number'>30</div>
						<div class='platform'>Linked In</div>
					</div>
					<div class='column'>
						<div class='number'>30</div>
						<div class='platform'>Instagram</div>
					</div>
				</div>

				-->
			</div>
		</div>
		<div class='speaker-header-menu'>
			<div class='wrap'>
				<div class='speaker-header-menu-nav'>
					<a href='#speaker-profile'>Profile</a>
					<a href='#speaker-expertise'>Expertise</a>
					<a href='#speaker-media'>Media</a>
					<a href='#speaker-feedback'>Feedback</a>
				</div>
			</div>
		</div>
		<div class='speaker-content'>
			<div class='wrap'>
				<div id='speaker-profile' class='speaker-profile'>
					<h3>Profile</h3>
					<div class='speaker-profile-content'>
						<div class='left'>
							<?php
								$content = apply_filters( 'the_content', get_the_content() );
								$start = strpos( $content, '<p>' );
								$end = strpos( $content, '</p>', $start );
								$left = substr( $content, $start, $end - $start + 4 );
								$right = substr( $content, $end - $start + 4 );

								echo $left;
							?>
						</div>
						<div class='right'>
							<?php echo $right; ?>
						</div>
					</div>
				</div>

				<div id='speaker-expertise' class='speaker-meta'>
					<h3>Expertise</h3>
					<div class='speaker-meta-content'>
						<?php
							$talking_points = get_field( 'speaker_topics' );
							$first = true;

							if ( $talking_points )
							{
								foreach ( $talking_points as $talking_point )
								{
									if ( array_key_exists('Type', $talking_point) && $talking_point['Type'] === 'Topic' )
									{
										if ( $first )
										{
											?>
												<div class='left'>
													<div class='heading'>Talking Points</div>
											<?php
											$first = false;
										}
										?>
										<div class='talking-point'>
											<div class='title'>
												<i class='fa-solid fa-chevron-up hidden'></i>
												<i class='fa-solid fa-chevron-down'></i>
												<?php if ( !is_array( $talking_point['Author'] ) ) echo $talking_point['Author']; ?>
											</div>
											<div class='text hidden'><?php if ( !is_array( $talking_point['Text'] ) ) echo $talking_point['Text']; ?></div>
										</div>
										<?php
									}
								}

								if ( !$first ) echo '</div>';
							}
						?>
						<div class='right'>
							<?php $topics = explode(' / ', substr($category_string, 0, -3)); ?>
							<?php if ( $topics ) : ?>
								<div class='heading'>Topics</div>
								
								<?php foreach ( $topics as $topic ) : ?>
									<div class='topic'>
										<?php echo $topic; ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<?php
					$media_videos = [];
					foreach ( get_field( 'speaker_videos' ) as $fileitem )
					{
						if ( isset($fileitem['Type']) && $fileitem['Type'] == 'VideoLink' )
							$media_videos[] = $fileitem;
					}
				?>

				<?php if ( !empty($media_videos) ) : ?>
					<div id='speaker-media' class='speaker-media'>
						<h3>Media</h3>
						<div class='speaker-media-list'>
							<?php foreach ( $media_videos as $video ) : ?>
								<?php preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['Name'], $match); ?>

								<div class='speaker-video carousel-cell'>
									<iframe src='https://www.youtube.com/embed/<?php echo $match[1]; ?>?controls=0' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php
					$testimonials = [];
					foreach ( get_field( 'speaker_topics' ) as $topic )
					{
						if ( isset($topic['Type']) && $topic['Type']  == 'Endorsement' )
							$testimonials[] = $topic;
					}
				?>

				<?php if ( $testimonials ) : ?>
					<div id='speaker-feedback'>
						<h3>Feedback</h3>
						<div class='testimonial-list'>
							<?php foreach ( $testimonials as $testimonial ) : ?>	
								<div class='feedback'>
									<div class='quote'><?php echo $testimonial['Text']; ?></div>
									<div class='author'>- <?php echo $testimonial['Author']; ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php
}

# Genesis
genesis();