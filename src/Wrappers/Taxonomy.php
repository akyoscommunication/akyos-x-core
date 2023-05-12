<?php

namespace Akyos\Core\Wrappers;

use Extended\ACF\Location;

class Taxonomy
{
	private array $fields = [];

	public function __construct(
        private string $slug,
        private string $title,
        private string $title_plural,
        private string $url_rewrite,
        private mixed $posttypes,
    ){
		$this->createTaxonomy();
	}

	public function createTaxonomy(): self
	{
        if (!$this->posttypes) return $this;

		register_taxonomy($this->slug, $this->posttypes, [
			'label' => $this->title_plural,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'show_in_quick_edit' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			'query_var' => true,
			'rewrite' => [
				'slug' => $this->url_rewrite,
				'with_front' => false,
			],
		]);

        return $this;
    }

	public function make(): void
	{
		register_extended_field_group([
			'title' => $this->title,
			'fields' => $this->fields,
			'name' => str_replace(' ', '_', strtolower($this->slug)) . '_fields_group',
			'position' => 'acf_after_title',
			'style' => 'default',
			'location' => [
				Location::where('taxonomy', '==', $this->slug)
			],
		]);
	}

    public function setPostTypes($posttypes): self
    {
        $this->posttypes = $posttypes;

        return $this;
    }

	public static function register(string $slug, string $title, string $title_plural, string $url_rewrite, ?array $posttypes = null): Taxonomy
	{
		return new Taxonomy($slug, $title, $title_plural, $url_rewrite, $posttypes);
	}

	public function fields(array $fields): Taxonomy
	{
		$this->fields = array_merge($this->fields, $fields);
		return $this;
	}
}
