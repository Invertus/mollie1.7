<?php

namespace Mollie\Service;

use Mollie;

class LegacyTranslator implements TranslatorInterface
{
	/**
	 * @var Mollie
	 */
	private $module;

	public function __construct(Mollie $module)
	{
		$this->module = $module;
	}

	/**
	 * @param string $key
	 * @param array $parameters
	 * @param string $domain
	 *
	 * @return string
	 */
	public function trans($key, $parameters, $domain)
	{
		$message = str_replace(
			array_keys($parameters),
			array_values($parameters),
			$key
		);

		return $this->module->l($message, $domain);
	}
}
