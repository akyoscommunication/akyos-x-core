<?php

namespace Akyos\Core\Wrappers;

use Akyos\Core\Interface\IBootable;

class Router implements IBootable
{
	
	public static function hook(): string { return "rest_api_init"; }
	public static function boot(): void
	{
		$post_types_php = get_template_directory() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'router.php';
		try {
			require_once $post_types_php;
		} catch(\Exception $e) {
			wp_die("Error: unable to find app/router.php");
		}
	}
	
	private static Router $instance;
	private string $basePath;
	private array $routes = [];
	
	public function __construct(string $basePath)
	{
		$this->basePath = $basePath;
		self::$instance = $this;
	}
	
	public static function instance(): Router
	{
		return self::$instance;
	}
	
	public function getBasePath(): string
	{
		return $this->basePath;
	}
	
	public function getRoutes(): array
	{
		return $this->routes;
	}
	
	public function addRoute(string $method, string $path, object $data): int
	{
		$this->routes[] = (object) [
			'id' => count($this->routes),
			'http_method' => $method,
			'controller' => $data->controller,
			'method' => $data->method,
			'path' => $path
		];
		return end($this->routes)->id;
	}
	
	public function groupRoute(string $basePath, int $route_id): void
	{
		$route = $this->routes[$route_id];
		$route->path = $basePath . $route->path;
	}
	
	private function registerRoutes(): void
	{
		foreach ($this->routes as $route) {
			$this->register($route);
		}
	}
	
	private function register($route): void
	{
		$path = $route->path;
		$controller = $route->controller;
		$method = $route->method;
		
		$path = $this->handleWildcards($path);
		
		register_rest_route($this->basePath, $path, [
			'methods' => $route->http_method,
			'callback' => function () use ($controller, $method) {
				return (new $controller())->$method();
			},
			'permission_callback' => '__return_true',
		]);
	}
	
	private function handleWildcards(string $path): string
	{
		return preg_replace('/\{([a-zA-Z0-9]+)\}/', '(?P<$1>[a-zA-Z0-9-]+)', $path);
	}
	
}
