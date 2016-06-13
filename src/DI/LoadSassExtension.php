<?php

namespace Sw2\LoadSass\DI;

use Nette;
use Sw2\Load\DI\LoadExtension;
use Sw2\LoadSass\SassCompiler;
use Sw2\LoadSass\SassMacros;

/**
 * Class LoadSassExtension
 *
 * @package Sw2\SassLoad\Di
 */
class LoadSassExtension extends LoadExtension
{
	/** @var array */
	public $defaults = [
		'debugger' => FALSE,
		'genDir' => 'webtemp',
		'files' => [],
	];

	/**
	 * Processes configuration data. Intended to be overridden by descendant.
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
		$wwwDir = $builder->parameters['wwwDir'];
		$genDir = $config['genDir'];

		if (!is_writable("$wwwDir/$genDir")) {
			throw new Nette\IOException("Directory '$wwwDir/$genDir' is not writable.");
		}

		$args = [$builder->parameters['debugMode'], $wwwDir, $genDir, $config['files']];
		$this->addCompilerDefinition('sass', SassCompiler::class, $args);
		$this->registerMacros(SassMacros::class);
		$this->registerDebugger($config['debugger']);
	}

}
