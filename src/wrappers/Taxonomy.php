<?php

namespace Akyos\Core\Wrappers;

use Extended\ACF\Location;

class Taxonomy
{
	
	private string $slug;
	private string $title;
	private string $title_plural;
	private string $url_rewrite;
	private array $fields = [];
	
	public function __construct(string $slug, string $title, string $title_plural, string $url_rewrite)
	{
		$this->slug = $slug;
		$this->title = $title;
		$this->title_plural = $title_plural;
		$this->url_rewrite = $url_rewrite;
		$this->createTaxonomy();
	}
	
	private function createTaxonomy(): void
	{
		register_taxonomy($this->slug, ['post'], [
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
	
	public static function register(string $slug, string $title, string $title_plural, string $url_rewrite): Taxonomy
	{
		return new Taxonomy($slug, $title, $title_plural, $url_rewrite);
	}
	
	public function fields(array $fields): Taxonomy
	{
		$this->fields = array_merge($this->fields, $fields);
		return $this;
	}
}
