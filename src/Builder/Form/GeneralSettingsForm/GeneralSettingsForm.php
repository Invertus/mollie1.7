<?php

namespace Mollie\Builder\Form\GeneralSettingsForm;

use Mollie;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Utility\TagsUtility;

class GeneralSettingsForm implements TemplateBuilderInterface
{
    /**
     * @var Mollie
     */
    private $module;

    /**
     * @var PaymentMethodBlock
     */
    private $paymentMethodBlock;

    public function __construct(Mollie $module, PaymentMethodBlock $paymentMethodBlock)
    {
        $this->module = $module;
        $this->paymentMethodBlock = $paymentMethodBlock;
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
            [
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

        $input[] = [
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
        ];

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
        $input = array_merge($input, $this->paymentMethodBlock->buildParams());

        return $input;
    }
}