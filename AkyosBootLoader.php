<?php

use Akyos\Core\ACF\CustomFields;
use Akyos\Core\Classes\Block;
use Akyos\Core\Interface\AkyosBootableInterface;
use Akyos\Core\Wrappers\Directives;
use Akyos\Core\Wrappers\PostType;
use Akyos\Core\Wrappers\Router;
use Illuminate\Support\Collection;

class AkyosBootLoader {
	
	private Collection $classes;

	public function __construct($classes)
	{
		$this->classes = collect([
			PostType::class,
			Router::class,
			Directives::class,
			Block::class,
			CustomFields::class,
		]);
	}
	
	public function load(): void
	{
		$this->classes->each(function ($class) {
			if($class instanceof AkyosBootableInterface) {
				$this->loadClass($class);
			}
		});
	}
	
	private function loadClass(mixed $class): void
	{
		add_action($class->hook() ?? 'after_setup_theme', function () use ($class) {
			$class = new $class;
			if(method_exists($class, 'boot')) { $class->boot(); }
		});
	}
	
}