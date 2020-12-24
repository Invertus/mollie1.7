<?php

namespace Mollie\Builder\Form\GeneralSettingsForm;

use Mollie;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Utility\EnvironmentUtility;
use Mollie\Utility\TagsUtility;

class GeneralSettingsForm implements TemplateBuilderInterface
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
     * @inheritDoc
     */
    public function buildParams()
    {
        return [
            [
                'form' => [
                    'input' => $this->getFormInputs(),
                    'submit' => [
                        'title' => $this->module->l('Save'),
                        'class' => 'btn btn-default pull-right',
                    ],
                ],
            ],
        ];
    }

    private function getFormInputs()
    {
        $input = [
            [
                'type' => 'mollie-support',
                'name' => '',
            ],
        ];

        if (EnvironmentUtility::getApiKey()) {
            $input[] =
                [
                    'type' => 'select',
                    'label' => $this->module->l('Environment', 'FormBuilder'),
                    'name' => Config::MOLLIE_ENVIRONMENT,
                    'options' => [
                        'query' => [
                            [
                                'id' => Config::ENVIRONMENT_TEST,
                                'name' => $this->module->l('Test', 'FormBuilder'),
                            ],
                            [
                                'id' => Config::ENVIRONMENT_LIVE,
                                'name' => $this->module->l('Live', 'FormBuilder'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ];
            $input[] = [
                'type' => 'mollie-password',
                'label' => $this->module->l('API Key Test', 'FormBuilder'),
                'desc' => TagsUtility::ppTags(
                    $this->module->l('You can find your API key in your [1]Mollie Profile[/1]; it starts with test or live.', 'FormBuilder'),
                    [$this->module->display($this->module->getPathUri(), 'views/templates/admin/profile.tpl')]
                ),
                'name' => Config::MOLLIE_API_KEY_TEST,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-test-api-group',
            ];
            $input[] = [
                'type' => 'mollie-password',
                'label' => $this->module->l('API Key Live', 'FormBuilder'),

                'name' => Config::MOLLIE_API_KEY,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-live-api-group',
            ];
            $input[] = [
                'type' => 'mollie-password',
                'label' => $this->module->l('Profile ID', 'FormBuilder'),

                'desc' => TagsUtility::ppTags(
                    $this->module->l('You can find your Profile ID in your [1]Mollie Profile[/1]', 'FormBuilder'),
                    [$this->module->display($this->module->getPathUri(), 'views/templates/admin/profile.tpl')]
                ),
                'name' => Config::MOLLIE_PROFILE_ID,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-api-profile-id',
            ];
            $input[] = [
                'type' => 'mollie-button',
                'label' => '',

                'name' => Config::MOLLIE_API_KEY_TESTING_BUTTON,
                'text' => $this->module->l('Test ApiKey', 'FormBuilder'),
                'class' => 'js-test-api-keys',
                'form_group_class' => 'js-api-key-test',
            ];
            $input[] =
                [
                    'type' => 'mollie-h3',

                    'name' => '',
                    'title' => '',
                ];
        } else {
            $input[] =
                [
                    'type' => 'mollie-switch',
                    'label' => $this->module->l('Do you already have a Mollie account?', 'FormBuilder'),
                    'name' => Config::MOLLIE_ACCOUNT_SWITCH,

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
                    'desc' => $this->module->display(
                        $this->module->getPathUri(), 'views/templates/admin/create_new_account_link.tpl'
                    ),
                ];
            $input[] = [
                'type' => 'select',
                'label' => $this->module->l('Environment', 'FormBuilder'),

                'name' => Config::MOLLIE_ENVIRONMENT,
                'options' => [
                    'query' => [
                        [
                            'id' => Config::ENVIRONMENT_TEST,
                            'name' => $this->module->l('Test', 'FormBuilder'),
                        ],
                        [
                            'id' => Config::ENVIRONMENT_LIVE,
                            'name' => $this->module->l('Live', 'FormBuilder'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ];
            $input[] = [
                'type' => 'mollie-password',
                'label' => $this->module->l('API Key Test', 'FormBuilder'),

                'desc' => TagsUtility::ppTags(
                    $this->module->l('You can find your API key in your [1]Mollie Profile[/1]; it starts with test or live.', 'FormBuilder'),
                    [$this->module->display($this->module->getPathUri(), 'views/templates/admin/profile.tpl')]
                ),
                'name' => Config::MOLLIE_API_KEY_TEST,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-test-api-group',
            ];
            $input[] = [
                'type' => 'mollie-password',
                'label' => $this->module->l('API Key Live', 'FormBuilder'),

                'name' => Config::MOLLIE_API_KEY,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-live-api-group',
            ];
        }
        if (!EnvironmentUtility::getApiKey()) {
            return $input;
        }
        $input[] = [
            'type' => 'switch',
            'label' => $this->module->l('Use Mollie Components for CreditCards', 'FormBuilder'),

            'name' => Config::MOLLIE_IFRAME,
            'desc' => TagsUtility::ppTags(
                $this->module->l('Read more about [1]Mollie Components[/1] and how it improves your conversion.', 'FormBuilder'),
                [$this->module->display($this->module->getPathUri(), 'views/templates/admin/mollie_components_info.tpl')]
            ),
            $this->module->l('Read more about Mollie Components and how it improves your conversion', 'FormBuilder'),
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
        ];

        $input[] = [
            'type' => 'switch',
            'label' => $this->module->l('Use Single Click Payments for CreditCards', 'FormBuilder'),

            'name' => Config::MOLLIE_SINGLE_CLICK_PAYMENT,
            'desc' => TagsUtility::ppTags(
                $this->module->l('Read more about [1]Single Click Payments[/1] and how it improves your conversion.', 'FormBuilder'),
                [
                    $this->module->display($this->module->getPathUri(), 'views/templates/admin/mollie_single_click_payment_info.tpl'),
                ]
            ),
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
        ];

        $input = array_merge($input, [
                [
                    'type' => 'select',
                    'label' => $this->module->l('Issuer list', 'FormBuilder'),

                    'desc' => $this->module->l('Some payment methods (eg. iDEAL) have an issuer list. This setting specifies where it is shown.', 'FormBuilder'),
                    'name' => Config::MOLLIE_ISSUERS,
                    'options' => [
                        'query' => [
                            [
                                'id' => Config::ISSUERS_ON_CLICK,
                                'name' => $this->module->l('On click', 'FormBuilder'),
                            ],
                            [
                                'id' => Config::ISSUERS_PAYMENT_PAGE,
                                'name' => $this->module->l('Payment page', 'FormBuilder'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
            ]
        );

        $input[] = [
            'type' => 'mollie-button-update-order-total-restriction',
            'label' => '',

            'name' => Config::MOLLIE_BUTTON_ORDER_TOTAL_REFRESH,
            'text' => $this->module->l('Refresh order total restriction values', 'FormBuilder'),
            'class' => 'js-refresh-order-total-values',
            'form_group_class' => 'js-refresh-order-total',
            'help' => $this->module->l('Will refresh all available payment method order total restriction values by all currencies', 'FormBuilder'),
        ];

        $input[] = [
            'type' => 'mollie-h2',

            'name' => '',
            'title' => $this->module->l('Payment methods', 'FormBuilder'),
        ];
//        $molliePaymentMethods = $this->apiService->getMethodsForConfig($this->module->api, $this->module->getPathUri());

//        if (empty($molliePaymentMethods)) {
//            $input[] = [
//                'type' => 'mollie-payment-empty-alert',
//
//                'name' => '',
//            ];
//        }

        $dateStamp = Mollie\Utility\TimeUtility::getCurrentTimeStamp();
//        $input[] = [
//            'type' => 'mollie-methods',
//            'name' => Config::METHODS_CONFIG,
//            'paymentMethods' => $molliePaymentMethods,
//            'countries' => $this->countryService->getActiveCountriesList(),
//
//            'onlyOrderMethods' => array_merge(Config::KLARNA_PAYMENTS, ['voucher']),
//            'displayErrors' => Configuration::get(Config::MOLLIE_DISPLAY_ERRORS),
//            'methodDescription' => TagsUtility::ppTags(
//                $this->module->l('Click [1]here[/1] to read more about the differences between the Payment and Orders API.', 'FormBuilder'),
//                [
//                    $this->module->display($this->module->getPathUri(), 'views/templates/admin/mollie_method_info.tpl'),
//                ]
//            ),
//            'showCustomLogo' => Configuration::get(Config::MOLLIE_SHOW_CUSTOM_LOGO),
//            'customLogoUrl' => $this->creditCardLogoProvider->getLogoPathUri() . "?{$dateStamp}",
//            'customLogoExist' => $this->creditCardLogoProvider->logoExists(),
//            'voucherCategory' => Configuration::get(Config::MOLLIE_VOUCHER_CATEGORY),
//            'categoryList' => \Category::getCategories($this->module->getContext()->language->id, true, false),
//            'productAttributes' => Attribute::getAttributes($this->module->getContext()->language->id),
//            'klarnaPayments' => Config::KLARNA_PAYMENTS,
//            'klarnaStatuses' => [Config::MOLLIE_STATUS_KLARNA_AUTHORIZED, Config::MOLLIE_STATUS_KLARNA_SHIPPED],
//        ];
        
        return $input;
    }
}