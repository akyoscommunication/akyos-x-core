<?php


/**
 * Footer configuration
 */

add_action('widgets_init', function () {

	$columns = 4;
	if ($footer_layout = get_field('footer_layout', 'option')) {
		$columns = $footer_layout['footer_columns'];
	}

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
