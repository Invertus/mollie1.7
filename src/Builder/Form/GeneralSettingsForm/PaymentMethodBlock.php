<?php

namespace Mollie\Builder\Form\GeneralSettingsForm;

use Attribute;
use Category;
use Configuration;
use Mollie;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Provider\CreditCardLogoProvider;
use Mollie\Service\ApiService;
use Mollie\Service\CountryService;
use Mollie\Utility\TagsUtility;

class PaymentMethodBlock implements TemplateBuilderInterface
{
    /**
     * @var Mollie
     */
    private $module;

    /**
     * @var ApiService
     */
    private $apiService;

    /**
     * @var CountryService
     */
    private $countryService;

    /**
     * @var CreditCardLogoProvider
     */
    private $creditCardLogoProvider;

    public function __construct(
        Mollie $module,
        ApiService $apiService,
        CountryService $countryService,
        CreditCardLogoProvider $creditCardLogoProvider
    ) {
        $this->module = $module;
        $this->apiService = $apiService;
        $this->countryService = $countryService;
        $this->creditCardLogoProvider = $creditCardLogoProvider;
    }

    /**
     * @inheritDoc
     */
    public function buildParams()
    {
        $dateStamp = Mollie\Utility\TimeUtility::getCurrentTimeStamp();
        $molliePaymentMethods = $this->apiService->getMethodsForConfig($this->module->api, $this->module->getPathUri());
        $input = [];

        if (empty($molliePaymentMethods)) {
            $input[] = [
                'type' => 'mollie-payment-empty-alert',
                'name' => '',
            ];
        }

        $input[] = [
            'type' => 'mollie-methods',
            'name' => Config::METHODS_CONFIG,
            'paymentMethods' => $molliePaymentMethods,
            'countries' => $this->countryService->getActiveCountriesList(),
            'onlyOrderMethods' => array_merge(Config::KLARNA_PAYMENTS, ['voucher']),
            'displayErrors' => Configuration::get(Config::MOLLIE_DISPLAY_ERRORS),
            'methodDescription' => TagsUtility::ppTags(
                $this->module->l('Click [1]here[/1] to read more about the differences between the Payment and Orders API.', 'FormBuilder'),
                [
                    $this->module->display($this->module->getPathUri(), 'views/templates/admin/mollie_method_info.tpl'),
                ]
            ),
            'showCustomLogo' => Configuration::get(Config::MOLLIE_SHOW_CUSTOM_LOGO),
            'customLogoUrl' => $this->creditCardLogoProvider->getLogoPathUri() . "?{$dateStamp}",
            'customLogoExist' => $this->creditCardLogoProvider->logoExists(),
            'voucherCategory' => Configuration::get(Config::MOLLIE_VOUCHER_CATEGORY),
            'categoryList' => Category::getCategories($this->module->getContext()->language->id, true, false),
            'productAttributes' => Attribute::getAttributes($this->module->getContext()->language->id),
            'klarnaPayments' => Config::KLARNA_PAYMENTS,
            'klarnaStatuses' => [Config::MOLLIE_STATUS_KLARNA_AUTHORIZED, Config::MOLLIE_STATUS_KLARNA_SHIPPED],
        ];

        return $input;

    }
}