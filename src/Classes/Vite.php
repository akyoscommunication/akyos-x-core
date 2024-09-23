<?php

namespace Akyos\Core\Classes;

use function Akyos\Core\Helpers\checkReachability;

const ENV_DEV = 'development';
class Vite {

	private static ?Vite $instance = null;
	public static function instance(): Vite
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private static string $devURL = 'http://127.0.0.1:1111';
	private static string $devJS = 'http://127.0.0.1:1111/resources/assets/js';
	private static string $devCSS = 'http://127.0.0.1:1111/resources/assets/css';
	private static string $bundle = 'public';

	private bool $dev = false;

	public function __construct()
	{
		if(WP_ENV == ENV_DEV) {
			$this->dev = checkReachability(Vite::$devURL);
		}
	}

	private function public(): string
	{
		return get_template_directory_uri() . DIRECTORY_SEPARATOR . Vite::$bundle;
	}

	private function getEntryPoints(): array
	{
		$entries = file_get_contents(get_template_directory() . DIRECTORY_SEPARATOR . Vite::$bundle . DIRECTORY_SEPARATOR . 'entrypoints.json');
		if($entries === false) {
			wp_die('Error: unable to find entrypoints.json');
		}
		return json_decode($entries, true);
	}

	private function getBundle($name): object
	{
		$entryPoints = $this->getEntryPoints();
		return (object) [
			'js' => $this->public() . DIRECTORY_SEPARATOR . $entryPoints[$name]['js'][0],
			'css' => $this->public() . DIRECTORY_SEPARATOR . $entryPoints[$name]['css'][0]
		];
	}

	public function isDev(): bool
	{
		return $this->dev;
	}

	public function script($name): void
	{
		if($this->isDev()) {
			echo '<script type="module" src="' . Vite::$devJS . DIRECTORY_SEPARATOR . $name . '.js"></script>';
		} else {
			echo '<script type="module" src="' . $this->getBundle($name)->js . '"></script>';
		}
	}

	public function style($name): void
	{
		if($this->isDev()) {
			echo '<link rel="stylesheet" href="' . Vite::$devCSS . DIRECTORY_SEPARATOR . $name . '.css" type="text/plain">';
		} else {
			echo '<link rel="stylesheet" href="' . $this->getBundle($name)->css . '" type="text/css">';
		}
	}

	public function enqueue($name): void
	{
		$this->script($name);
		!$this->isDev() ? $this->style($name) : null;
	}

}
