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
			if (isset($this->block['spacing']['margin']['top'])) {
				$mt = $this->block['spacing']['margin']['top'];
			}
			if (isset($this->block['spacing']['margin']['bottom'])) {
				$mb = $this->block['spacing']['margin']['bottom'];
			}
		}

		return [
			'--pt' => $pt,
			'--pb' => $pb,
			'--mt' => $mt,
			'--mb' => $mb,
		];
	}

	protected function outputClass(): array
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
			if (isset($this->block['spacing']['margin']['top'])) {
				$mt = $this->block['spacing']['margin']['top'];
			}
			if (isset($this->block['spacing']['margin']['bottom'])) {
				$mb = $this->block['spacing']['margin']['bottom'];
			}
		}

		return [
			'padding-top-'.$pt,
			'padding-bottom-'.$pb,
			'margin-top-'.$mt,
			'margin-bottom-'.$mb
		];
	}

	public function opt(): Attribute
	{
		return $this->attribute
			->setAttributeOpt([
				'spacing' => [
					'padding' => true,
					'margin' => true,
				]
			]);
	}
}
