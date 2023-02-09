<?php

namespace Akyos\Core\Interface;

interface AkyosBootableInterface
{
	public function hook(): string;
	
	public function boot(): void;
}