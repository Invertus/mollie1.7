<?php

namespace Mollie\Builder;

interface FormBuilderInterface
{
	/**
	 * Function used to add form block
	 *
	 * @param string $name Block name
	 * @param null $type Class name of type ex. TextType::class
	 * @param array $input Input configuration
	 *
	 * @return self
	 */
	public function add($name, $type = null, $input = []);

	/**
	 * @return array input configuration
	 */
	public function build();
}
