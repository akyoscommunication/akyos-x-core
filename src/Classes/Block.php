<?php

namespace Akyos\Core\Classes;

use Akyos\Core\Interface\IBootable;
use Extended\ACF\Fields\Group;
use Extended\ACF\Location;
use Roots\Acorn\View\Component;
use function App\get_block_styles;

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

    public static function make(string $label, string $id, $layout = 'table')
    {
        return Group::make($label, $id)->fields(static::fields())->layout($layout);
    }

  	public function renderCallback($block, $content = '', $is_preview = false)
	{
		$class = get_class($this);
		$instance_block = new $class();
		if ($fields = get_fields()) {
		    foreach ($fields as $key => $value) {
			    $instance_block->$key = $value;
		    }
		}

        $instance_block->data();

        $collect = collect((new \ReflectionObject($instance_block))->getProperties(\ReflectionProperty::IS_PUBLIC))
		    ->map(function (\ReflectionProperty $property) {
			return $property->getName();
		    })->all();

		$values = [];

		foreach ($collect as $property) {
		    $values[$property] = $instance_block->{$property};
		}

        $values['block'] = $block;

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
