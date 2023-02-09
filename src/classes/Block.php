<?php

namespace Akyos\Core\Classes;

use Akyos\Core\Interface\IBootable;
use Extended\ACF\Location;
use Roots\Acorn\View\Component;

abstract class Block extends Component implements IBootable
{
	
	public static function hook(): string { return 'init'; }
	public static function boot(): void
	{
		$path = get_template_directory() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'Blocks';
		$directory = new \RecursiveDirectoryIterator($path);
		$iterator = new \RecursiveIteratorIterator($directory);
		foreach ($iterator as $info) {
			if (preg_match('/\w*[.]php\b/', $info->getFileName())) {
				require_once $info->getPathName();
				$name = explode('.', $info->getFileName());
				$name = 'App\\View\\Blocks\\'.$name[0];
				(new $name())->registerGutenberg();
			}
		}
	}
	
	protected GutenbergBlock $gutenberg;
	public function registerBlock()
	{
		if (function_exists('acf_register_block_type')) {
			acf_register_block_type(array_merge($this->gutenberg->getOpts(), [
				'name'  => $this->gutenberg->getName(),
				'title' => __($this->gutenberg->getTitle()),
				'description' => __($this->gutenberg->getDescription()),
				'render_callback' => [$this, 'renderCallback'],
//                'enqueue_style' => get_template_directory_uri() . '/views/blocks/'.$this->gutenberg['rootDir'].'/'.$this->name.'.css',
				'category' => $this->gutenberg->getCategory(),
			]));
		}
	}
	
	abstract protected static function block(): GutenbergBlock;
	abstract protected static function fields(): array;
	
	public function renderCallback($block, $content = '', $is_preview = false)
	{
		if (get_fields()) {
			foreach (get_fields() as $key => $value) {
				$this->$key = $value;
			}
		}
		
		if (method_exists($this, 'data')) { $this->data(); }
		
		$collect = collect((new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC))
			->map(function (\ReflectionProperty $property) {
				return $property->getName();
			})->all();
		
		$values = [];
		
		foreach ($collect as $property) {
			$values[$property] = $this->{$property};
		}
		
		echo $this->render()->with($values);
	}
	
	public function registerGutenberg()
	{
		$this->gutenberg = $this::block();
		$this->registerBlock();
		
		if (function_exists('register_extended_field_group')) {
			register_extended_field_group([
				'title' => $this->gutenberg->getTitle().' Block',
				'fields' => static::fields(),
				'location' => [
					Location::where('block', 'acf/'.$this->gutenberg->getName())
				],
			]);
		}
	}
}
