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
use Mollie\Utility\TagsUtility;
use MolliePrefix\Mollie\Api\Types\OrderStatus;
use MolliePrefix\Mollie\Api\Types\PaymentStatus;
use MolliePrefix\Mollie\Api\Types\RefundStatus;
use OrderState;
use Tools;

class MailSettingsBlock implements TemplateBuilderInterface
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
        $input = [];

        $input[] = [
            'type' => 'select',
            'label' => $this->module->l('Send order confirmation email', 'FormBuilder'),
            'name' => Config::MOLLIE_SEND_ORDER_CONFIRMATION,
            'options' => [
                'query' => [
                    [
                        'id' => Config::ORDER_CONF_MAIL_SEND_ON_CREATION,
                        'name' => $this->module->l('When Order is created', 'FormBuilder'),
                    ],
                    [
                        'id' => Config::ORDER_CONF_MAIL_SEND_ON_PAID,
                        'name' => $this->module->l('When Order is Paid', 'FormBuilder'),
                    ],
                    [
                        'id' => Config::ORDER_CONF_MAIL_SEND_ON_NEVER,
                        'name' => $this->module->l('Never', 'FormBuilder'),
                    ],
                ],
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        if (Module::isEnabled(Config::EMAIL_ALERTS_MODULE_NAME)) {
            $input[] = [
                'type' => 'select',
                'label' => $this->module->l('Send new order email to merchant', 'FormBuilder'),
                'name' => Config::MOLLIE_SEND_NEW_ORDER,
                'desc' => TagsUtility::ppTags(
                    $this->module->l('Change when \'new_order\' email to merchant is sent (When using PrestaShop Mail Alerts module)', 'FormBuilder'),
                    [$this->module->display($this->module->getPathUri(), 'views/templates/admin/locale_wiki.tpl')]
                ),
                'options' => [
                    'query' => [
                        [
                            'id' => Config::NEW_ORDER_MAIL_SEND_ON_CREATION,
                            'name' => $this->module->l('When Order is created', 'FormBuilder'),
                        ],
                        [
                            'id' => Config::NEW_ORDER_MAIL_SEND_ON_PAID,
                            'name' => $this->module->l('When Order is Paid', 'FormBuilder'),
                        ],
                        [
                            'id' => Config::NEW_ORDER_MAIL_SEND_ON_NEVER,
                            'name' => $this->module->l('Never', 'FormBuilder'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ];
        }

        return $input;
    }
}