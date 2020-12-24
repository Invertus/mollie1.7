<?php

namespace Mollie\Builder\Form\AdvancedSettingsForm;

use Attribute;
use Category;
use Configuration;
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

class OrderStatusesSettingsBlock implements TemplateBuilderInterface
{
    /**
     * @var Mollie
     */
    private $module;

    /**
     * @var LegacyContext
     */
    private $context;

    public function __construct(
        Mollie $module,
        LegacyContext $context
    ) {
        $this->module = $module;
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function buildParams()
    {
        $input = [];

        $messageStatus = $this->module->l('Status for %s payments', 'FormBuilder');
        $descriptionStatus = $this->module->l('`%s` payments get status `%s`', 'FormBuilder');
        $messageMail = $this->module->l('Send mails when %s', 'FormBuilder');
        $descriptionMail = $this->module->l('Send mails when transaction status becomes %s?', 'FormBuilder');
        $allStatuses = OrderState::getOrderStates($this->context->getLanguageId());
        $allStatusesWithSkipOption = array_merge([['id_order_state' => 0, 'name' => $this->module->l('Skip this status', 'FormBuilder'), 'color' => '#565656']], $allStatuses);


        $statuses = [];
        foreach (Config::getStatuses() as $name => $val) {
            if (PaymentStatus::STATUS_AUTHORIZED === $name) {
                continue;
            }

            $val = (int) $val;
            if ($val) {
                $orderStatus = new OrderState($val);
                $statusName = $orderStatus->getFieldByLang('name', $this->context->getLanguageId());
                $desc = Tools::strtolower(
                    sprintf(
                        $descriptionStatus,
                        $this->module->lang($name),
                        $statusName
                    )
                );
            } else {
                $desc = sprintf($this->module->l('`%s` payments do not get a status', 'FormBuilder'), $this->module->lang($name));
            }
            $statuses[] = [
                'name' => $name,
                'key' => @constant('Mollie\Config\Config::MOLLIE_STATUS_' . Tools::strtoupper($name)),
                'value' => $val,
                'description' => $desc,
                'message' => sprintf($messageStatus, $this->module->lang($name)),
                'key_mail' => @constant('Mollie\Config\Config::MOLLIE_MAIL_WHEN_' . Tools::strtoupper($name)),
                'value_mail' => Configuration::get('MOLLIE_MAIL_WHEN_' . Tools::strtoupper($name)),
                'description_mail' => sprintf($descriptionMail, $this->module->lang($name)),
                'message_mail' => sprintf($messageMail, $this->module->lang($name)),
            ];
        }
        $input[] = [
            'type' => 'mollie-h2',
            'name' => '',
            'title' => $this->module->l('Order statuses', 'FormBuilder'),
        ];

        foreach (array_filter($statuses, function ($status) {
            return in_array($status['name'], [
                Config::MOLLIE_AWAITING_PAYMENT,
                PaymentStatus::STATUS_PAID,
                OrderStatus::STATUS_COMPLETED,
                PaymentStatus::STATUS_AUTHORIZED,
                PaymentStatus::STATUS_CANCELED,
                PaymentStatus::STATUS_EXPIRED,
                RefundStatus::STATUS_REFUNDED,
                PaymentStatus::STATUS_OPEN,
                Config::PARTIAL_REFUND_CODE,
                OrderStatus::STATUS_SHIPPING,
            ]);
        }) as $status) {
            if (!in_array($status['name'], [Config::PARTIAL_REFUND_CODE])) {
                $input[] = [
                    'type' => 'switch',
                    'label' => $status['message_mail'],
                    'name' => $status['key_mail'],
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
            }

            $isStatusAwaiting = Config::MOLLIE_AWAITING_PAYMENT === $status['name'];
            $input[] = [
                'type' => 'select',
                'label' => $status['message'],
                'desc' => $status['description'],
                'name' => $status['key'],
                'options' => [
                    'query' => $isStatusAwaiting ? $allStatuses : $allStatusesWithSkipOption,
                    'id' => 'id_order_state',
                    'name' => 'name',
                ],
            ];
        }

        return $input;
    }
}