<?php /* Template Name: Experts */

remove_action('genesis_loop', 'genesis_do_loop');

# After Header
remove_action('genesis_after_header', 'theme_after_header');
add_action('genesis_after_header', 'page_after_header');
function page_after_header()
{
?>
	<?php $page_header_banner = get_field('page_header_banner'); ?>
	<section class='after-header after-header--experts' style='background-image: url(<?php echo $page_header_banner; ?>);'>
		<div class='wrap'>
			<h1>Find an Expert</h1>
			<div class='main-search-row'>
				<form method='get' action='/' class='form-no-submit'>
					<?php $search = (isset($_GET['search'])) ? $_GET['search'] : ''; ?>
					<input type='search' id='main-search' placeholder='Search by name or topic' value='<?php echo $search; ?>' name='search'>
					<i data-lucide='search' class='icon icon--search'></i>
				</form>
			</div>
		</div>
	</section>

	<div class='search-page-content'>
		<div class='col col--left'>
			<?php
			$categories = get_terms([
				'taxonomy' 		=> 'expert-categories',
				'orderby' 		=> 'name',
				'order'   		=> 'DESC',
				'hide_empty'	=> false,
			]);

			foreach ($categories as $category) {
				# Hide children
				if ($category->parent === 0) {
					echo '<div class="heading" data-tier="1" data-id="' . $category->term_id . '">' . $category->name . '</div>';

					foreach ($categories as $child_category) {
						if ($child_category->parent === $category->term_id) {
							$checked = '';
							if (isset($_GET['categories']))
								$checked = ($_GET['categories'] == $category->term_id) ? 'checked' : '';

							echo '<div class="filter-row experts-filter-row">';
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
		<div class='col col--right'>
			<div class='main-results'>
				<?php
				$shortlist_experts = (isset($_COOKIE['experts'])) ? explode('|', $_COOKIE['experts']) : [];

				# Query
				$page = (isset($_GET['sp'])) ? $_GET['sp'] : 1;
				$args = [
					'post_type' 		=> 'experts',
					'posts_per_page'	=> 24,
					'paged'				=> $page,
					'orderby'			=> ['title' => 'ASC']
				];

				# Search
				if (isset($_GET['search']))
					$args['s'] = $_GET['search'];

				# Category
				if (isset($_GET['categories']))
					$args['tax_query'][] = [
						'taxonomy'	=> 'expert_categories',
						'field'		=> 'id',
						'terms'		=> $_GET['categories']
					];

				$query = new WP_Query($args);
				?>

				<?php while ($query->have_posts()) : $query->the_post(); ?>
					<?php $image = get_expert_thumbnail(get_the_ID()); ?>

					<a href='<?php the_permalink(); ?>' class='search-expert' style='background-image: linear-gradient(black, black)<?php if ($image) : ?>,url(<?php echo $image; ?>)<?php endif; ?>;' data-expert-id='<?php the_ID(); ?>'>
						<div class='add-button <?php if (in_array(get_the_ID(), $shortlist_experts)) echo 'active'; ?>' data-url='<?php echo get_permalink(1883); ?>'>
							<i data-lucide='plus' class='icon icon--plus <?php if (in_array(get_the_ID(), $shortlist_experts)) echo 'hidden'; ?>'></i>
							<i data-lucide='x' class='icon icon--x <?php if (!in_array(get_the_ID(), $shortlist_experts)) echo 'hidden'; ?>'></i>
						</div>
						<div class='search-expert-content'>
							<div class='name'>
								<?php echo str_replace(' ', "\n", get_the_title()); ?>
							</div>
							<div class='excerpt'>
								<?php echo get_field('tagline'); ?>
							</div>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
			<div class='spinner hidden'>
				<i data-lucide='loader-circle' class='icon icon--spinner'></i>
			</div>
			<div class='empty-results hidden'>
				No Results.
			</div>
			<div class='pagination experts-pagination'>
				<?php
				$page = (isset($_GET['sp'])) ? $_GET['sp'] : 1;
				echo paginate_links(array(
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
				?>
			</div>
		</div>
	</div>
<?php
}

# Genesis
genesis();
