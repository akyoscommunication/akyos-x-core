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

		if (isset($this->block['style']['spacing'])) {
			if (isset($this->block['style']['spacing']['padding']['top'])) {
				$pt = $this->block['style']['spacing']['padding']['top'];
			}
			if (isset($this->block['style']['spacing']['padding']['bottom'])) {
				$pb = $this->block['style']['spacing']['padding']['bottom'];
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
