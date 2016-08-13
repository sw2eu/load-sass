<?php

namespace Sw2\LoadSass\Bridges\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;


class SassMacros extends MacroSet
{

	/**
	 * @param Compiler $compiler
	 *
	 * @return static
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('sass', [$me, 'macroSass'], NULL, [$me, 'macroAttrSass']);

		return $me;
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroSass(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write("echo %escape(\$basePath . '/' . \$presenter->context->getService('sw2load.compiler.sass')->link(%node.word));");
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroAttrSass(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write("echo ' href=\"' . %escape(\$basePath . '/' . \$presenter->context->getService('sw2load.compiler.sass')->link(%node.word)) . '\"';");
	}

}
