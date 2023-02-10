<?php

/**
 * Register "Akyos" Gutenberg block category
 */

add_action('block_categories_all', function ($categories)
{
	return array_merge(
		[
			[
				'slug' => 'akyos',
				'title' => 'Akyos',
			],
			[
				'slug' => 'akyos_footer_blocks',
				'title' => 'Akyos Footer',
			],
		],
		$categories
	);
}, 10, 2);



/**
 * Footer configuration
 */

add_action('widgets_init', function () {
	
	$columns = isset(get_field('footer_layout', 'option')['footer_columns']) ?? 4;
	
	$config = [
		'before_widget' => '<div class="widget mb-4">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	];
	
	for ($i = 1; $i <= $columns; $i++) {
		register_sidebar([
				'name' => 'Footer colonne '.$i,
				'id' => 'footer-column-'.$i
			] + $config);
	}
	
});
