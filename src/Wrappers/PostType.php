<?php

namespace Akyos\Core\Wrappers;

use Akyos\Core\Interface\IBootable;
use Extended\ACF\Location;

class PostType implements IBootable
{

	public static function hook(): string { return "init"; }
	public static function boot(): void
	{
		$routes_php = get_template_directory() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'post_types.php';
		try {
			require_once $routes_php;
		} catch(\Exception $e) {
			wp_die("Error: unable to find app/post_types.php");
		}
	}

	private array $taxonomies = [];
	private array $fields = [];
    private array $config = [];

    private array $supports = [
        'title',
        'thumbnail',
        'excerpt',
    ];

    public function __construct(
        private string $slug,
        private string $title,
        private string $title_plural,
        private string $url_rewrite,
        private string $icon,
        private bool $show_in_rest,
        private bool $has_archive = false
    ){}

	private function createPostType(): void
	{
		register_post_type($this->slug, array_merge([
            'label' => $this->title_plural,
            'menu_icon' => 'dashicons-' . $this->icon,
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'capability_type' => 'post',
            'hierarchical'    => true,
            'query_var'       => true,
            'show_in_rest' => $this->show_in_rest,
            'has_archive' => ($this->has_archive) ? $this->url_rewrite : false,
            'rewrite' => [
                'slug' => $this->url_rewrite,
                'with_front' => false,
            ],
            'supports' => $this->supports
        ], $this->config));
	}

	public function make(): void
	{
        $this->createPostType();
		$this->registerTaxonomies();
		register_extended_field_group([
			'title' => $this->title,
			'fields' => $this->fields,
			'name' => str_replace(' ', '_', strtolower($this->slug)) . '_fields_group',
			'position' => 'acf_after_title',
			'style' => 'default',
			'location' => [
				Location::where('post_type', '===', $this->slug),
			],
		]);
	}

    public function supports(array $arr): self
    {
        $this->supports = $arr;
        return $this;
    }

    public function setConfig(array $arr): self
    {
        $this->config = $arr;

        return $this;
    }

	private function registerTaxonomies(): void
	{
        /** @var Taxonomy $taxonomy */
        foreach ($this->taxonomies as $taxonomy) {
			$taxonomy->setPostTypes($this->slug)->createTaxonomy()->make();
		}
	}

	public static function register(string $slug, string $title, string $title_plural, string $url_rewrite, string $icon, bool $show_in_rest, bool $has_archive = false): PostType
	{
		if (post_type_exists($slug)) {
			wp_die('Unable to register post type ' . $slug . ' because it already exists.');
		}
		return new self($slug, $title, $title_plural, $url_rewrite, $icon, $show_in_rest, $has_archive);
	}

	public function taxonomies(array $taxonomies): PostType
	{
		$this->taxonomies = array_merge($this->taxonomies, $taxonomies);
		return $this;
	}

	public function fields(array $fields): PostType
	{
		$this->fields = array_merge($this->fields, $fields);
		return $this;
	}
}
