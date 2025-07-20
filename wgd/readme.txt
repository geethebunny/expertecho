1. Add this code to functions.php.

# WebGee Designs
require_once get_stylesheet_directory() . '/wgd/wp-mobile-menu.php';



2. If mobile menu will be different than Primary Menu, create a new WordPress menu.



3. Add shortcode or widget to "Header Right" widget area.

[mobile_menu arg="val"]

	Arguements:
		menu		- Slug of menu. Defaults to Primary Menu.
		color
			black	- (default) Black button, white hover
			white	- White button, black hover
		align
			none	- (default) No float
			left	- (default) Float left
			right	- Float right