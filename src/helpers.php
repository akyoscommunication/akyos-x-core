<?php

namespace Akyos\Core;

function icon($icon): void
{
	$file = get_template_directory() . "/resources/assets/icons/{$icon}.svg";
	$file = str_replace("/", DIRECTORY_SEPARATOR, $file);
	if(!file_exists($file)) { echo (WP_ENV === "development") ? "<!-- Icon {$icon} does not exists -->" : ""; }
	include $file;
}