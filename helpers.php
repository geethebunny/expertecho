<?php

/**
 * Get expert thumbnail image.
 *
 * Returns the ACF 'thumbnail' image URL or a fallback placeholder.
 *
 * @param int $expert_id    ID of the expert.
 * @return string Image URL.
 *
 * @since 1.0.0
 */
function get_expert_thumbnail($expert_id)
{
	$image = get_field('thumbnail', $expert_id);

	if (!$image)
		$image = get_stylesheet_directory_uri() . '/images/ASB_Placeholder_1.png';
	else
		$image = $image['url'];

	return $image;
}



/**
 * Get expert hero background image.
 *
 * Returns an image URL that's 1920x960 or a fallback placeholder.
 *
 * @param int $expert_id    ID of the expert.
 * @return string Image URL.
 *
 * @since 1.0.0
 */
function get_expert_hero_bg($expert_id)
{
	$images = get_field('speaker_images', $expert_id);
	$image = $images;

	if (is_array($images)) {
		$image = null;

		foreach ($images as $data) {
			$size = @getimagesize(speakers_image_url($data['url']));

			if (is_array($size))
				if ($size[0] == 1920 && $size[1] == 960)
					$image = speakers_image_url($data['url']);
		}
	}

	if (!$image)
		$image = get_stylesheet_directory_uri() . '/images/ASB_Placeholder_3.png';

	return $image;
}
