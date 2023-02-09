<?php

namespace Akyos\Core\ACF;

use Akyos\Core\Interface\IBootable;

class CustomFields implements IBootable
{
	
	const DirectoryIdentifier = "Field";
	
	public function hook(): string { return 'after_setup_theme'; }
	public function boot(): void
	{
		$fieldsDirectory = scandir(__DIR__ . DIRECTORY_SEPARATOR . 'CustomFields');
		foreach ($fieldsDirectory as $items) {
			if (preg_match('/\w*'.self::DirectoryIdentifier.'\b/', $items)) {
				$name = str_replace(self::DirectoryIdentifier, "", $items) . '.php';
				$path = __DIR__ . DIRECTORY_SEPARATOR . $items . DIRECTORY_SEPARATOR . $name;
				if (is_file($path)) {
					$cls = str_replace('.php', '', "Akyos\\Core\\ACF\\CustomFields\\{$items}\\{$name}");
					$cls = new $cls;
					$cls->register();
				}
			}
		}
	}
}
