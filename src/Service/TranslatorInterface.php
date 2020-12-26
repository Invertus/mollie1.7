<?php

namespace Mollie\Service;

interface TranslatorInterface
{
	public function trans($key, $parameters, $domain);
}
