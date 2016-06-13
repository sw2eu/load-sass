<?php

namespace Sw2\LoadSass;

use Leafo\ScssPhp\Compiler;
use Nette\Caching\Cache;
use Sw2\Load\DI\LoadExtension;
use Sw2\Load\ICompiler;

/**
 * Class SassCompiler
 *
 * @package Sw2\SassLoad
 */
class SassCompiler implements ICompiler
{
	/** @var Cache */
	private $cache;

	/** @var bool */
	private $debugMode;

	/** @var string */
	private $wwwDir;

	/** @var string */
	private $genDir;

	/** @var array */
	private $files;

	/** @var array */
	private $statistics = [];

	/**
	 * @param Cache $cache
	 * @param bool $debugMode
	 * @param string $wwwDir
	 * @param string $genDir
	 * @param array $files
	 */
	public function __construct(Cache $cache, $debugMode, $wwwDir, $genDir, array $files)
	{
		$this->cache = $cache;
		$this->debugMode = $debugMode;
		$this->wwwDir = $wwwDir;
		$this->genDir = $genDir;
		$this->files = $files;
	}

	/**
	 * @return array
	 */
	public function getStatistics()
	{
		return $this->statistics;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function link($name)
	{
		$path = $this->cache->load([$name, $this->debugMode]);
		$file = $this->files[$name];
		if ($path === NULL) {
			$time = LoadExtension::computeMaxTime('scss', $file);
			$hash = LoadExtension::computeHash($file, $time, $this->debugMode);
			$path = "{$this->genDir}/$name-$hash.css";
			$this->cache->save([$name, $this->debugMode], $path);
		}

		$genFile = "{$this->wwwDir}/$path";
		if (!file_exists($genFile) || ($this->debugMode && filemtime($genFile) < (isset($time) ? $time : ($time = LoadExtension::computeMaxTime('scss', $file))))) {
			$start = microtime(TRUE);
			$compiler = new Compiler;
			$compiler->addImportPath(dirname($file));
			file_put_contents($genFile, $compiler->compile(file_get_contents($file), $file));
			if ($this->debugMode) {
				$this->statistics[$name]['time'] = microtime(TRUE) - $start;
				$this->statistics[$name]['parsedFiles'] = $compiler->getParsedFiles();
			}
		}
		if ($this->debugMode) {
			$this->statistics[$name]['size'] = filesize($genFile);
			$this->statistics[$name]['file'] = $file;
			$this->statistics[$name]['date'] = isset($time) ? $time : ($time = LoadExtension::computeMaxTime('scss', $file));
			$this->statistics[$name]['path'] = $path;
		}

		return $path;
	}

}

