<?php

use Akyos\Core\Interface\IBootable;

class ExtraFields implements IBootable {
	
	public static function hook(): string { return 'after_setup_theme'; }
	public static function boot(): void
	{
		$acf_dir = str_replace('/', DIRECTORY_SEPARATOR, get_template_directory() . '/app/Acf');
		if(!is_dir($acf_dir)) { wp_die("Error: unable to find app/Acf directory"); }
		
		collect(scandir($acf_dir))->each(function ($item) use ($acf_dir) {
			if(str_ends_with($item, '.php')) {
				$path = $acf_dir . DIRECTORY_SEPARATOR . $item;
				if(is_file($path)) { require_once $path; }
			}
		});
		
	}
	
}