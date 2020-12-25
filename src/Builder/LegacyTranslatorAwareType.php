<?php

namespace Mollie\Builder;

use Mollie\Service\TranslatorInterface;

class LegacyTranslatorAwareType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $key
     * @param string $domain
     * @param array $parameters ex. ['%orderId%' => 15]
     *
     * @return mixed
     */
    protected function trans($key, $domain, $parameters = [])
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}