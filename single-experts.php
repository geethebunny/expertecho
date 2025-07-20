<?php

# Remove Content
remove_action( 'genesis_loop', 'genesis_do_loop' );

# After Header
remove_action( 'genesis_after_header', 'theme_after_header' );
add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	$image = get_field( 'hero_image' );
	$categories = wp_get_post_terms( get_the_ID(), 'expert_categories' );

    if ( !$image )
        $image['url'] = get_stylesheet_directory_uri() . '/images/ASB_Placeholder_3.png';
	?>
		<div class='after-header' style='background-image: url(<?php echo $image['url']; ?>);'>
			<div class='wrap'>
				<h1><?php the_title(); ?></h1>
				<div class='tagline'><?php echo get_field( 'tagline' ); ?></div>
			</div>
		</div>
		<div class='speaker-basic-info'>
			<div class='wrap'>
				<div class='heading'>
					<?php
						$category_string = '';
						foreach ( $categories as $category )
						{
							$category_string .= '<a href="' . get_category_link($category->term_id). '">' . $category->name . '</a> / ';
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
								<i class='fa fa-plus'></i> Add to Expert Enquiry
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
			</div>
		</div>
		<div class='speaker-header-menu'>
			<div class='wrap'>
				<div class='speaker-header-menu-nav'>
					<a href='#speaker-profile'>Profile</a>
					<a href='#speaker-expertise'>Talking Points</a>
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
							<?php echo get_field( 'short_description'); ?>
						</div>
						<div class='right'>
                        <?php echo get_field( 'description'); ?>
						</div>
					</div>
				</div>

				<div id='speaker-expertise' class='speaker-meta'>
					<h3>Talking Points</h3>
					<div class='speaker-meta-content'>
						<?php
							$talking_points = get_field( 'talking_points' );

							if ( $talking_points )
							{
								foreach ( $talking_points as $talking_point )
								{
                                    ?>
                                    <div class='talking-point'>
                                        <div class='title'>
                                            <i class='fa-solid fa-chevron-up hidden'></i>
                                            <i class='fa-solid fa-chevron-down'></i>
                                            <?php echo $talking_point['heading']; ?>
                                        </div>
                                        <div class='text hidden'><?php echo $talking_point['text']; ?></div>
                                    </div>
                                    <?php
								}
							}
						?>
					</div>
				</div>

				<?php
					$media = get_field('media');
				?>
				<?php if ( !empty($media) ) : ?>
					<div id='speaker-media' class='speaker-media'>
						<h3>Media</h3>
						<div class='speaker-media-list'>
							<?php foreach ( $media as $video ) : ?>
								<?php preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['url'], $match); ?>

								<div class='speaker-video carousel-cell'>
									<iframe src='https://www.youtube.com/embed/<?php echo $match[1]; ?>?controls=0' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php $feedback = get_field( 'feedback' ); ?>

				<?php if ( $feedback ) : ?>
					<div id='speaker-feedback'>
						<h3>Feedback</h3>
						<div class='testimonial-list'>
							<?php foreach ( $feedback as $testimonial ) : ?>	
								<div class='feedback'>
									<div class='quote'><?php echo $testimonial['text']; ?></div>
									<div class='author'>- <?php echo $testimonial['author']; ?></div>
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