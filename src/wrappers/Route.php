<?php

namespace Akyos\Core\Wrappers;

class Route
{
	
	public static function get(string $path, string $controller): int
	{
		$data = Route::parse($controller);
		return self::router()->addRoute('GET', $path, $data);
	}
	
	public static function post(string $path, string $controller): int
	{
		$data = Route::parse($controller);
		return self::router()->addRoute('POST', $path, $data);
	}
	
	public static function group(string $prefix, array $route_ids)
	{
		array_map(function ($route_id) use ($prefix) {
			self::router()->groupRoute($prefix, $route_id);
		}, $route_ids);
	}
	
	private static function router(): Router
	{
		return Router::instance();
	}
	
	private static function parse(string $controller): object
	{
		$data = explode('@', $controller);
		$controller = 'App\\Controllers\\' . $data[0];
		$method = $data[1];
		return (object) ['controller' => $controller, 'method' => $method];
	}
}
