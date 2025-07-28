<?php

remove_action('genesis_loop', 'genesis_do_loop');

remove_action('genesis_after_header', 'theme_after_header');
add_action('genesis_after_header', 'page_after_header');
function page_after_header()
{
?>
	<?php $hero = get_field('hero_area'); ?>
	<section class='after-header after-header--home' style='background-image:url(<?php echo $hero['background_image']; ?>)'>
		<div class='wrap'>
			<h1><?php echo $hero['heading']; ?></h1>
			<div class='text'><?php echo $hero['text']; ?></div>
			<a href='<?php echo $hero['button_url']; ?>' class='button'><?php echo $hero['button_text'] ?></a>
		</div>
	</section>

	<?php $after_hero = get_field('after_hero_area'); ?>
	<section class='about-section'>
		<div class='wrap'>
			<div class='text'><?php echo $after_hero['text']; ?></div>
		</div>
	</section>

	<section class='categories-section'>
		<div class='wrap'>
			<?php
			$category_string = '';
			$categories = get_terms([
				'taxonomy' 			=> 'expert-categories',
				'orderby' 			=> 'name',
				'order'   			=> 'ASC',
				'hide_empty'		=> false
			]);

			shuffle($categories);
			$categories = array_slice($categories, 0, 5);

			foreach ($categories as $category) {
				$category_string .= '<a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a> • ';
			}

			echo substr($category_string, 0, -4);
			?>
			<div class='button-block'>
				<a href='<?php echo get_permalink(32); ?>' class='button'>Find an Expert</a>
			</div>
		</div>
	</section>

	<?php $experts = get_field('speakers_area'); ?>
	<section class='blue-section'>
		<div class='wrap cols'>
			<div class='col col--left'>
				<div class='text'><?php echo $experts['left_text']; ?></div>
			</div>
			<div class='col col--right'>
				<div class='text'><?php echo $experts['right_text']; ?></div>
			</div>
		</div>
	</section>

	<section class='links-section'>
		<div class='wrap cols'>
			<div class='col'>
				<a href='<?php echo $experts['column_1']['link']; ?>' style='background-image: url(<?php echo $experts['column_1']['image']; ?>);'><?php echo $experts['column_1']['text']; ?></a>
			</div>
			<div class='col'>
				<a href='<?php echo $experts['column_2']['link']; ?>' style='background-image: url(<?php echo $experts['column_2']['image']; ?>);'><?php echo $experts['column_2']['text']; ?></a>
			</div>
			<div class='col'>
				<a href='<?php echo $experts['column_3']['link']; ?>' style='background-image: url(<?php echo $experts['column_3']['image']; ?>);'><?php echo $experts['column_3']['text']; ?></a>
			</div>
		</div>
	</section>

	<section class='text-section'>
		<div class='wrap'>
			<?php echo get_field('text_area'); ?>
		</div>
	</section>
<?php
}



genesis();
