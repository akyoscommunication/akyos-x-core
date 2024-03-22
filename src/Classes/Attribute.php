<?php

namespace Akyos\Core\Classes;

class Attribute
{

	public $outputClass = [];
	public $outputStyle = [];
	public $attributeOpt = [];

	public function getOutputClass(): array
	{
		return $this->outputClass;
	}

	public function setOutputClass(array $outputClass): self
	{
		$this->outputClass = $outputClass;
		return $this;
	}

	public function getOutputStyle(): array
	{
		return $this->outputStyle;
	}

	public function setOutputStyle(array $outputStyle): self
	{
		$this->outputStyle = $outputStyle;
		return $this;
	}

	public function getAttributeOpt(): array
	{
		return $this->attributeOpt;
	}

	public function setAttributeOpt(array $attributeOpt): self
	{
		$this->attributeOpt = $attributeOpt;
		return $this;
	}
}
