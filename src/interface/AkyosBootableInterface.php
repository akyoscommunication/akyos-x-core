<?php

namespace Akyos\Core\Interface;

interface AkyosBootableInterface
{
	public static function hook(): string;
	
	public static function boot(): void;
}