<?php

namespace Akyos\Core\Attributes;

use Akyos\Core\Classes\Attribute;
use Akyos\Core\Classes\AttributeClass;

class Spacing extends AttributeClass
{
	protected function outputStyle(): array
	{
		$pt = null;
		$pb = null;

		if (isset($this->block['spacing'])) {
			if (isset($this->block['spacing']['padding']['top'])) {
				$pt = $this->block['spacing']['padding']['top'];
			}
			if (isset($this->block['spacing']['padding']['bottom'])) {
				$pb = $this->block['spacing']['padding']['bottom'];
			}
		}

		return [
			'--pt' => $pt,
			'--pb' => $pb
		];
	}

	public function opt(): Attribute
	{
		return $this->attribute
			->setAttributeOpt([
				'spacing' => [
					'padding' => true
				]
			]);
	}
}
