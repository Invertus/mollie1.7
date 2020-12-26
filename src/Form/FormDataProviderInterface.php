<?php

namespace Mollie\Form;

interface FormDataProviderInterface
{
	/**
	 * Used to get configuration values
	 *
	 * @return array
	 */
	public function getData();

	/**
	 * Used to update configuration values
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function setData(array $data);
}
