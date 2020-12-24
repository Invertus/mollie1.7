<?php

namespace Mollie\Builder\Form\AdvancedSettingsForm;

use Attribute;
use Category;
use Configuration;
use Module;
use Mollie;
use Mollie\Adapter\LegacyContext;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Provider\CreditCardLogoProvider;
use Mollie\Service\ApiService;
use Mollie\Service\CountryService;
use Mollie\Service\MolCarrierInformationService;
use Mollie\Utility\AssortUtility;
use Mollie\Utility\TagsUtility;
use MolliePrefix\Mollie\Api\Types\OrderStatus;
use MolliePrefix\Mollie\Api\Types\PaymentStatus;
use MolliePrefix\Mollie\Api\Types\RefundStatus;
use OrderState;
use Tools;

class ShipmentSettingsBlock implements TemplateBuilderInterface
{
    /**
     * @var Mollie
     */
    private $module;

    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var MolCarrierInformationService
     */
    private $carrierInformationService;

    public function __construct(Mollie $module, LegacyContext $context, MolCarrierInformationService $carrierInformationService)
    {
        $this->module = $module;
        $this->context = $context;
        $this->carrierInformationService = $carrierInformationService;
    }

    /**
     * @inheritDoc
     */
    public function buildParams()
    {
        $orderStatuses = [];
        $orderStatuses = array_merge($orderStatuses, OrderState::getOrderStates($this->context->getLanguageId()));

        $input = [];

        $input[] = [
            'type' => 'mollie-carriers',
            'label' => $this->module->l('Shipment information', 'FormBuilder'),
            'name' => Config::MOLLIE_TRACKING_URLS,
            'depends' => Config::MOLLIE_API,
            'depends_value' => Config::MOLLIE_ORDERS_API,
            'carriers' => $this->carrierInformationService->getAllCarriersInformation($this->context->getLanguageId()),
        ];
        $input[] = [
            'type' => 'mollie-carrier-switch',
            'label' => $this->module->l('Automatically ship on marked statuses', 'FormBuilder'),
            'name' => Config::MOLLIE_AUTO_SHIP_MAIN,
            'desc' => $this->module->l('Enabling this feature will automatically send shipment information when an order gets marked status.', 'FormBuilder'),
            'is_bool' => true,
            'values' => [
                [
                    'id' => 'active_on',
                    'value' => true,
                    'label' => $this->module->l('Enabled', 'FormBuilder'),
                ],
                [
                    'id' => 'active_off',
                    'value' => false,
                    'label' => $this->module->l('Disabled', 'FormBuilder'),
                ],
            ],
            'depends' => Config::MOLLIE_API,
            'depends_value' => Config::MOLLIE_ORDERS_API,
        ];
        $input[] = [
            'type' => 'checkbox',
            'label' => $this->module->l('Automatically ship when one of these statuses is reached', 'FormBuilder'),
            'desc' => $this->module->l('If an order reaches one of these statuses the module will automatically send shipment information', 'FormBuilder'),
            'name' => Config::MOLLIE_AUTO_SHIP_STATUSES,
            'multiple' => true,
            'values' => [
                'query' => $orderStatuses,
                'id' => 'id_order_state',
                'name' => 'name',
            ],
            'expand' => (count($orderStatuses) > 10) ? [
                'print_total' => count($orderStatuses),
                'default' => 'show',
                'show' => ['text' => $this->module->l('Show', 'FormBuilder'), 'icon' => 'plus-sign-alt'],
                'hide' => ['text' => $this->module->l('Hide', 'FormBuilder'), 'icon' => 'minus-sign-alt'],
            ] : null,
            'depends' => Config::MOLLIE_API,
            'depends_value' => Config::MOLLIE_ORDERS_API,
        ];

        return $input;
    }
}