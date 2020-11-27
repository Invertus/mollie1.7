<?php

$finder = PhpCsFixer\Finder::create()
	->exclude(array('.git', 'vendor', 'vendorBuilder'))
	->in(__DIR__);

return PhpCsFixer\Config::create()
	->setIndent("\t")
	->setRules([
		'@PSR2' => true,
		'@Symfony' => true
	])
	->setFinder($finder);
