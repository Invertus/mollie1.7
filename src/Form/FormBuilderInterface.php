<?php

namespace Mollie\Form;

interface FormBuilderInterface
{
	/**
	 * Adds new block to builder by Type, or Options.
	 * ex. If Type is specified and it can be retrieved by DI then it takes all blocks from that FormType and inserts it to this FormBuilder
	 * ex. If Options are specified then a new block with given configuration
	 *
	 * @param string $child
	 * @param null $type
	 * @param array $options
	 *
	 * @return self
	 */
	public function add($child, $type = null, array $options = []);

	/**
	 * Creates the form.
	 *
	 * @return FormInterface The form
	 */
	public function getForm();

	/**
	 * Legacy builder surrogate to get block configuration.
	 *
	 * @return array
	 */
	public function build();
}
