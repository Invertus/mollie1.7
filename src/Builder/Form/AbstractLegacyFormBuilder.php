<?php

namespace Mollie\Builder\Form;

use Mollie;
use Mollie\Builder\FormBuilderInterface;
use Mollie\Builder\TypeInterface;

abstract class AbstractLegacyFormBuilder implements FormBuilderInterface
{
	/**
	 * @var array
	 */
	private $blocks = [];

	/**
	 * @var Mollie
	 */
	private $module;

	public function __construct(Mollie $module)
	{
		$this->module = $module;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add($name, $type = null, $input = [])
	{
		if ($type) {
			/** @var TypeInterface $typeBlock */
			$typeBlock = $this->module->getMollieContainer($type);

			if ($typeBlock) {
				$this->blocks[] = $typeBlock->buildForm($this, $input);

				return $this;
			}
		}
		$this->blocks[] = $input;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function build()
	{
		return $this->blocks;
	}
}
