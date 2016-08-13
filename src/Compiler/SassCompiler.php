<?php

namespace Sw2\LoadSass\Compiler;

use Leafo\ScssPhp\Compiler;
use Nette\Utils\Finder;
use Sw2\Load\Compiler\BaseCompiler;
use Sw2\Load\Helpers;

/**
 * Class SassCompiler
 *
 * @package Sw2\SassLoad
 */
class SassCompiler extends BaseCompiler
{

	/**
	 * @param array $sourceFiles
	 * @return int
	 */
	protected function getModifyTime($sourceFiles)
	{
		$time = 0;
		foreach ($sourceFiles as $sourceFile) {
			/** @var \SplFileInfo $file */
			foreach (Finder::find("*.scss")->from(dirname($sourceFile)) as $file) {
				$time = max($time, $file->getMTime());
			}
		}
		return $time;
	}

	/**
	 * @param string $name
	 * @param array $files
	 * @param int $time
	 * @param bool $debugMode
	 * @return string
	 */
	protected function getOutputFilename($name, $files, $time, $debugMode)
	{
		return $name . '-' . Helpers::computeHash($files, $time, $debugMode) . '.css';
	}

	/**
	 * @param array $sourceFiles
	 * @param string $outputFile
	 * @return array
	 */
	protected function compile($sourceFiles, $outputFile)
	{
		$parsedFiles = [];
		$file = fopen($outputFile, 'w');
		foreach ($sourceFiles as $sourceFile) {
			$compiler = new Compiler;
			$compiler->addImportPath(dirname($sourceFile));
			fwrite($file, $compiler->compile(file_get_contents($sourceFile), $sourceFile) . "\n");
			fflush($file);

			$parsedFiles = array_merge($parsedFiles, $compiler->getParsedFiles());
		}
		fclose($file);
		return $parsedFiles;
	}

}

