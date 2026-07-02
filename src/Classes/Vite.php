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

	private static string $bundle = 'public';

	private bool $dev = false;
	private string $devBaseUrl = '';

	public function __construct()
	{
		if (WP_ENV != ENV_DEV) {
			return;
		}

		$baseUrl = self::resolveDevBaseUrl();
		if ($baseUrl !== null && checkReachability($baseUrl)) {
			$this->dev = true;
			$this->devBaseUrl = $baseUrl;
		}
	}

	private static function resolveDevBaseUrl(): ?string
	{
		$hotFile = get_template_directory() . '/public/hot';
		if (is_readable($hotFile)) {
			$url = trim((string) file_get_contents($hotFile));
			if ($url !== '') {
				return rtrim($url, '/');
			}
		}

		$port = self::readHmrPort();
		if ($port !== null) {
			return "http://127.0.0.1:{$port}";
		}

		return null;
	}

	private static function readHmrPort(): ?int
	{
		$envFile = get_template_directory() . '/.env';
		if (!is_readable($envFile)) {
			return null;
		}

		foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
			if (!str_starts_with($line, 'HMR_PORT=')) {
				continue;
			}
			$port = (int) trim(substr($line, strlen('HMR_PORT=')), " \t'\"");
			return $port > 0 ? $port : null;
		}

		return null;
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
			echo '<script type="module" src="' . $this->devBaseUrl . '/resources/assets/js/' . $name . '.js"></script>';
		} else {
			echo '<script type="module" src="' . $this->getBundle($name)->js . '"></script>';
		}
	}

	public function style($name): void
	{
		if($this->isDev()) {
			echo '<link rel="stylesheet" href="' . $this->devBaseUrl . '/resources/assets/css/' . $name . '.css" type="text/css">';
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
