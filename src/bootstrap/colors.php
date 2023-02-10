<?php

/**
 * Custom color palette
 */

add_action('acf/init', function () {
	$palette = get_field('color_palette', 'option');
	if (!$palette) {
		$palette = [
			"primary" => "#0041b9",
			"secondary" => "#b59023",
			"third" => "#e1e9ef",
			"text" => "#29355b",
			"white" => "#ffffff",
			"grey" => "#9a9a9a",
			"black" => "#282828",
		];
	}
	add_theme_support('editor-color-palette', array(
		array( 'name' => 'Blanc ', 'slug' => 'white', 'color' => $palette["white"] ),
		array( 'name' => 'Noir', 'slug' => 'black', 'color' => $palette["black"]  ),
		array( 'name' => 'Gris', 'slug' => 'grey', 'color' => $palette["grey"]  ),
		array( 'name' => 'Primaire', 'slug' => 'primary', 'color' => $palette["primary"] ),
		array( 'name' => 'Secondaire', 'slug' => 'secondary', 'color' => $palette["secondary"] ),
		array( 'name' => 'Tertiaire', 'slug' => 'third', 'color' => $palette["third"] ),
		array( 'name' => 'Couleur de texte', 'slug' => 'text_color', 'color' => $palette["text"] ),
	));
});

/**
 * @param $hex The Heaxadecimal color
 * @return array The corresponding red, green, and blue components
 */
function get_rgb_from_hex($hex)
{
	if (strlen($hex) > 4) {
		# Full color code like #010203
		$red = hexdec(substr($hex, 1, 2));
		$green = hexdec(substr($hex, 3, 2));
		$blue = hexdec(substr($hex, 5, 2));
	} else {
		# Half color code like #123
		$red = hexdec(str_repeat(substr($hex, 1, 1), 2));
		$green = hexdec(str_repeat(substr($hex, 2, 1), 2));
		$blue = hexdec(str_repeat(substr($hex, 3, 1), 2));
	}
	
	return [
		"r" => $red,
		"g" => $green,
		"b" => $blue
	];
}

/**
 * @param $r Red component (0..255)
 * @param $g Green component (0..255)
 * @param $b Blue component (0..255)
 * @return array The corresponding hue, saturation and lightness components
 */
function get_hsl_from_rgb($r, $g, $b)
{
	# Normalize colors
	$r /= 255.0;
	$g /= 255.0;
	$b /= 255.0;
	
	$c_max = max($r, $g, $b);
	$c_min = min($r, $g, $b);
	$delta = $c_max - $c_min;
	
	# Lightness (0..1)
	$l = ($c_max + $c_min) / 2;
	
	# Saturation (0..1)
	$s = ($delta == 0) ? 0 : $delta / (1 - abs(2 * $l - 1) );
	
	# Hue (0..360°)
	$h = 0;
	if ($delta != 0) {
		switch ($c_max) {
			case $r:
				$h = 60 * fmod(($g - $b) / $delta, 6);
				break;
			case $g:
				$h = 60 * ( (($b - $r) / $delta) + 2 );
				break;
			case $b:
			default:
				$h = 60 * ( (($r - $g) / $delta) + 4 );
				break;
		}
	}
	
	return [
		"h" => round($h, 0),
		"s" => 100 * round($s, 2),
		"l" => 100 * round($l, 2)
	];
}

/**
 * @param $color The hexadecimal color value to add as CSS variable
 * @param $color_name The color name to add as CSS variable name
 * @return string The inline style for the color and its rgb and hsl components
 */
function get_color_with_component_style($color, $color_name)
{
	# Default color : --color: #fff
	$res = "--" . $color_name . ":" . $color . ";";
	
	# Gets RGB values from Hex code
	$rgb = get_rgb_from_hex($color);
	
	# RGB components : --color-red : 255    (0..255)
	$res .= "--" . $color_name . "-red:" . $rgb["r"] . ";";
	$res .= "--" . $color_name . "-green:" . $rgb["g"] . ";";
	$res .= "--" . $color_name . "-blue:" . $rgb["b"] . ";";
	
	# Gets HSL values from RGB components
	$hsl = get_hsl_from_rgb($rgb["r"], $rgb["g"], $rgb["b"]);
	
	$res .= "--" . $color_name . "-hue:" . $hsl["h"] . ";";
	$res .= "--" . $color_name . "-saturation:" . $hsl["s"] . "%;";
	$res .= "--" . $color_name . "-lightness:" . $hsl["l"] . "%;";
	
	return $res;
}
function echo_color_palette_style() {
	$palette = get_field('color_palette', 'option');
	$style = '<style> :root {';
	if(!$palette){
		return;
	}
	foreach ($palette as $color_name => $color) {
		$style .= get_color_with_component_style($color, $color_name);
	}
	$style .= '}</style>';
	echo $style;
}
add_action('wp_head', 'App\\echo_color_palette_style');
add_action('admin_head', 'App\\echo_color_palette_style');
