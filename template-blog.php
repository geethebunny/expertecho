<?php /* Template Name: Blog */

remove_action( 'genesis_loop', 'genesis_do_loop' );

# After Header
remove_action( 'genesis_after_header', 'theme_after_header' );
add_action( 'genesis_after_header', 'page_after_header' );
function page_after_header ()
{
	?>
		<div class='after-header'>
			<div class='wrap'>
				<h1>Blog</h1>
			</div>
		</div>
		<div class='search-page-content'>
			<div class='left'>
				<div class='search-row'>
					<form method='get' action='/' class='form-no-submit'>
						<?php $search = ''; ?>
						<?php if ( isset($_GET['search']) ) $search = $_GET['search']; ?>
						<input type='search' id='blog-search' placeholder='Search keywords' value='<?php echo $search; ?>' name='search'>
						<i class='fa-solid fa-magnifying-glass input-search-icon'></i>
					</form>
				</div>
				<div class='heading'>Topics</div>
				<?php
					$categories = get_categories();
				?>

				<?php foreach ( $categories as $category ) : ?>
					<div class='filter-row'>
						<input type='checkbox' name='<?php echo $category->slug; ?>' value='<?php echo $category->term_id; ?>'> <?php echo $category->name; ?></a>
					</div>
				<?php endforeach; ?>
			</div>
			<div class='tablet-only mobile-toggle'>
				<i class='fa-solid fa-chevron-down'></i>
				<i class='fa-solid fa-chevron-up'></i>
			</div>
			<div class='right'>
				<div class='main-results'>
					<?php
						$args = [
							'post_type' 		=> 'post',
							'posts_per_page'	=> -1,
							'order'				=> 'DESC'
						];

						# Search
						if ( isset($_GET['search']) )
							$args['s'] = $_GET['search'];

						# Category
						if ( isset($_GET['categories']) )
							$args['tax_query'][] = [
								'taxonomy'	=> 'Categories',
								'field'		=> 'id',
								'terms'		=> $_GET['categories']
							];

						$query = new WP_Query($args);
					?>

					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'single-post-thumbnail' ); ?>

						<a href='<?php the_permalink(); ?>' class='search-speaker' style='background-image: linear-gradient(black, black)<?php if ( $image ) : ?>,url(<?php echo $image[0]; ?>)<?php endif; ?>;'>
							<div class='search-speaker-content'>
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
					<?php endwhile; ?>
				</div>
				<div class='spinner hidden'>
					<i class='fas fa-circle-notch fa-spin'></i>
				</div>
				<div class='empty-results hidden'>
					No Results.
				</div>
			</div>
		</div>
	<?php
}

# Genesis
genesis();