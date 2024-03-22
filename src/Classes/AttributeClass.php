<?php

namespace Akyos\Core\Classes;

abstract class AttributeClass
{
	public Attribute $attribute;
	public array $block;

	public function __construct()
	{
		$this->block = [];
		$this->attribute = (new Attribute());
	}

	protected function outputStyle(): array
	{
		return [];
	}

	protected function outputClass(): array
	{
		return [];
	}

	/**
	 * @return Attribute
	 */
	public function opt(): Attribute
	{
		return $this->attribute;
	}

	public function setBlock(array $block)
	{
		$this->block = $block;
		$this->attribute->setOutputStyle($this->outputStyle());
		$this->attribute->setOutputClass($this->outputClass());
		return $this;
	}
}
