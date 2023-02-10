<?php

namespace Akyos\Core;

use Akyos\Core\ACF\CustomFields;
use Akyos\Core\Classes\Block;
use Akyos\Core\Interface\IBootable;
use Akyos\Core\Wrappers\Directives;
use Akyos\Core\Wrappers\PostType;
use Akyos\Core\Wrappers\Router;
use Illuminate\Support\Collection;

class AkyosBootLoader
{
	
	private Collection $classes;
	private Collection $bootstrap;
	
	public function __construct()
	{
		$this->checkRequirements();
		
		$this->classes = collect([
			PostType::class,
			Router::class,
			Directives::class,
			Block::class,
			CustomFields::class,
		]);
		
		$this->bootstrap = collect([
			'security', 'theme', 'colors'
		]);
		
	}
	
	public function load(): void
	{
		// Load bootstrap files
		$this->bootstrap->each(function ($file) {
			$bootstrap = str_replace("/", DIRECTORY_SEPARATOR, __DIR__ . "/bootstrap/{$file}.php");
			if(file_exists($bootstrap)) { require_once $bootstrap; }
			else { wp_die("Error: unable to find {$bootstrap}.php"); }
		});
		
		// Load classes
		$this->classes->each(function ($class) {
			$reflection = new \ReflectionClass($class);
			if ($reflection->implementsInterface(IBootable::class)) {
				$this->loadClass($class);
			}
		});
	}
	
	private function loadClass(mixed $class): void
	{
		add_action($class::hook() ?? 'after_setup_theme', function () use ($class) {
			if (method_exists($class, 'boot')) {
				$class::boot();
			}
		});
	}
	
	private function checkRequirements(): void
	{
		
		$reqs = collect([
			[
				"passed" => function_exists('get_fields'),
				"message" => "Akyos Core requires ACF Pro to be installed and activated."
			]
		]);
		
		$reqs->each(function ($req) {
			if (!$req['passed']) { wp_die($req['passed']); }
		});
		
	}
	
}
