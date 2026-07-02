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

add_action('after_setup_theme', function () {
	if (!function_exists('view')) {
		return;
	}

	$accessViews = get_template_directory() . '/vendor/akyos/akyos-access/resources/views';
	if (!is_dir($accessViews)) {
		return;
	}

	view()->addNamespace('akyos-access', $accessViews);

	// Priorité sur le image.blade.php legacy du thème (MediaAccess = tableaux ACF)
	if (class_exists(\Illuminate\Support\Facades\Blade::class)) {
		\Illuminate\Support\Facades\Blade::component('akyos-access::components.image', 'image');
		\Illuminate\Support\Facades\Blade::component('akyos-access::components.media', 'media');
	}
}, 100);

add_action('cli_init', static function (): void {
	if (!class_exists('WP_CLI') || !class_exists(\Akyos\Access\Console\AkyosAccessCommand::class)) {
		return;
	}

	\WP_CLI::add_command('akyos-access', \Akyos\Access\Console\AkyosAccessCommand::class);
});