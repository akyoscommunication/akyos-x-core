<?php

namespace Akyos\Core\Wrappers;

use Akyos\Core\Interface\IBootable;
use Illuminate\Support\Facades\Blade;

class Directives implements IBootable
{
	
	public static function hook(): string { return 'after_setup_theme'; }
	public static function boot(): void
	{
		self::instance()->registerDirectives();
	}
	
	private static ?Directives $instance = null;
	public static function instance(): Directives
	{
		if(self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function registerDirectives(): void
	{
		$this->iconDirective();
		$this->imageDirective();
		$this->thumbnailDirective();
		$this->menuDirective();
        $this->shortcodeDirective();
	}
	
	
	/**
	 * Blade directive for icons
	 * @icon(string name)
	 */
	private function iconDirective()
    {
        Blade::directive('icon', function ($name) {
            $file = get_template_directory() . "/resources/assets/icons/{$expression}.svg";
            if(!file_exists($file)) { return WP_ENV === 'development' ? "<!-- Icon {$expression} does not exists -->" : ''; }
            return "<?php include '$file'; ?>";
        });
    }
	
	
	/**
	 * Blade directive for images
	 * @images(int id, string size = 'full')
	 */
	private function imageDirective(): void
	{
		Blade::directive('images', function ($expression) {
			return "<?php echo wp_get_attachment_image({$expression}); ?>";
		});
	}
	
	
	/**
	 * Blade directive for post thumbnail
	 * @thumbnail(int | WP_Post post, string size = 'full'
	 */
	private function thumbnailDirective(): void
	{
		Blade::directive('thumbnail', function ($expression) {
			return "<?php echo get_the_post_thumbnail({$expression}); ?>";
		});
	}

	private function menuDirective()
    {
        Blade::directive('menu', function($expression){
            return "<?php echo wp_nav_menu(['theme_location' => $expression]) ?>";
        });
    }

    private function shortcodeDirective()
    {
        Blade::directive('shortcode', function($e){
            return "<?php echo do_shortcode($e) ?>";
        });
    }
	
	
}
