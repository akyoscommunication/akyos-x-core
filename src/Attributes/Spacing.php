<?php

namespace Akyos\Core\Attributes;

use Akyos\Core\Classes\Attribute;
use Akyos\Core\Classes\AttributeClass;

class Spacing extends AttributeClass
{
	protected function outputStyle(): array
	{
		$res = [];

		if (isset($this->block['style']['spacing'])) {
			if (isset($this->block['style']['spacing']['padding']['top'])) {
				$res['--pt'] = $this->block['style']['spacing']['padding']['top'];
			}
			if (isset($this->block['style']['spacing']['padding']['bottom'])) {
				$res['--pb'] = $this->block['style']['spacing']['padding']['bottom'];
			}
		}

		return $res;
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
