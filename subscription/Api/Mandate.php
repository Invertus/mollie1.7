<?php

declare(strict_types=1);

namespace Mollie\Subscription\Api;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\Mandate as MandateMollie;
use Mollie\Subscription\DTO\CreateMandateData;
use Mollie\Subscription\Factory\MollieApi;

class Mandate
{
    /** @var MollieApiClient */
    private $apiClient;

    public function __construct(MollieApi $mollieApiFactory)
    {
        $this->apiClient = $mollieApiFactory->getMollieClient();
    }

    /**
     * @return BaseResource|MandateMollie
     */
    public function createMandate(CreateMandateData $mandateData)
    {
        return $this->apiClient->mandates->createForId($mandateData->getCustomerId(), $mandateData->jsonSerialize());
    }
}
