<?php

namespace Akyos\Core\Interface;

interface IBootable
{
	public static function hook(): string;
	
	public static function boot(): void;
}