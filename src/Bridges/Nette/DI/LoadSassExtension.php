<?php

namespace Sw2\LoadSass\Bridges\Nette\DI;

use Sw2\Load\Bridges\Nette\DI\LoadExtension;
use Sw2\LoadSass\Bridges\Latte\SassMacros;
use Sw2\LoadSass\Compiler\SassCompiler;

/**
 * Class LoadSassExtension
 *
 * @package Sw2\SassLoad\Di
 */
class LoadSassExtension extends LoadExtension
{

	public function beforeCompile()
	{
		$this->addCompilerDefinition('sass', SassCompiler::class);
		$this->registerMacros(SassMacros::class);
		$this->registerDebugger();
	}

}
