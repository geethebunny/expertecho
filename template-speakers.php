<?php /* Template Name: Speakers */

remove_action( 'genesis_loop', 'genesis_do_loop' );

# After Header
remove_action( 'genesis_after_header', 'theme_after_header' );
add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	?>
		<div class='after-header'>
			<div class='wrap'>
				<h1>Find an Expert</h1>
				<div class='search-row'>
					<form method='get' action='/' class='form-no-submit'>
						<?php $search = ( isset($_GET['search']) ) ? $_GET['search'] : '' ; ?>
						<input type='search' id='main-search' placeholder='Search by name or topic' value='<?php echo $search; ?>' name='search'>
						<i class='fa-solid fa-magnifying-glass input-search-icon'></i>
					</form>
				</div>
			</div>
		</div>
		<div class='search-page-content'>
			<div class='left'>
				<?php
					$categories = get_terms([
						'taxonomy' 		=> 'expert-categories',
						'orderby' 		=> 'name',
						'order'   		=> 'DESC',
						'hide_empty'	=> false,
					]);

					foreach ( $categories as $category )
					{
						# Hide children
						if ( $category->parent === 0 )
						{
							echo '<div class="heading" data-tier="1" data-id="' . $category->term_id . '">' . $category->name . '</div>';

							foreach ( $categories as $child_category )
							{
								if ( $child_category->parent === $category->term_id )
								{
									$checked = '';
									if ( isset($_GET['categories']) )
										$checked = ($_GET['categories'] == $category->term_id ) ? 'checked' : '' ;

									echo '<div class="filter-row speakers-filter-row">';
									echo "<input type='checkbox' data-tier='2' name='$child_category->slug' value='$child_category->term_id' $checked> $child_category->name";
									echo '</div>';
								}
							}
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
						$shortlist_speakers = ( isset($_COOKIE['speakers']) ) ? explode( '|', $_COOKIE['speakers'] ) : [] ;

						# Query
						$page = ( isset($_GET['sp']) ) ? $_GET['sp'] : 1;
						$args = [
							'post_type' 		=> 'experts',
							'posts_per_page'	=> 24,
							'paged'				=> $page,
							'orderby'			=> ['title' => 'ASC']
						];

						# Search
						if ( isset($_GET['search']) )
							$args['s'] = $_GET['search'];

						# Category
						if ( isset($_GET['categories']) )
							$args['tax_query'][] = [
								'taxonomy'	=> 'expert_categories',
								'field'		=> 'id',
								'terms'		=> $_GET['categories']
							];

						$query = new WP_Query($args);
					?>

					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php $image = get_expert_thumbnail( get_the_ID() ); ?>

						<a href='<?php the_permalink(); ?>' class='search-speaker' style='background-image: linear-gradient(black, black)<?php if ( $image ) : ?>,url(<?php echo $image; ?>)<?php endif; ?>;' data-speaker-id='<?php the_ID(); ?>'>
							<div class='add-button' data-url='<?php echo get_permalink(1883); ?>'>
								<i class='fas fa-plus <?php if ( in_array( get_the_ID(), $shortlist_speakers) ) echo 'hidden'; ?>'></i>
								<i class='fas fa-times <?php if ( !in_array( get_the_ID(), $shortlist_speakers) ) echo 'hidden'; ?>'></i>
							</div>
							<div class='search-speaker-content'>
								<div class='name'>
									<?php echo str_replace(' ',"\n", get_the_title()); ?>
								</div>
								<div class='excerpt'>
									<?php echo get_field( 'tagline' ); ?>
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
