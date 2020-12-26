<?php

namespace Mollie\Form\Admin\AdvancedSettings\Type;

use Mollie;
use Mollie\Builder\LegacyTranslatorAwareType;
use Mollie\Config\Config;
use Mollie\Form\FormBuilderInterface;
use Mollie\Form\TypeInterface;
use Mollie\Utility\EnvironmentUtility;
use Mollie\Utility\TagsUtility;

class LegacyAdvancedSettingsType extends LegacyTranslatorAwareType implements TypeInterface
{
    /**
     * @var Mollie
     */
    private $module;

    public function setModule(Mollie $module)
    {
        $this->module = $module;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Information-top' , null, [
                'type' => 'mollie-support',
                'name' => '',
            ])
        ;

        $builder
            ->add(Config::MOLLIE_PAYMENTSCREEN_LOCALE, null, [
                'type' => 'select',
                'label' => $this->module->l('Push Locale to Payment Screen', 'FormBuilder'),
                'desc' => TagsUtility::ppTags(
                    $this->module->l('When activated, Mollie will use your webshop\'s [1]Locale[/1]. If not, the browser\'s locale will be used.', 'FormBuilder'),
                    [$this->module->display($this->module->getPathUri(), 'views/templates/admin/locale_wiki.tpl')]
                ),
                'name' => Config::MOLLIE_PAYMENTSCREEN_LOCALE,
                'options' => [
                    'query' => [
                        [
                            'id' => Config::PAYMENTSCREEN_LOCALE_SEND_WEBSITE_LOCALE,
                            'name' => $this->module->l('Yes, push webshop Locale', 'FormBuilder'),
                        ],
                        [
                            'id' => Config::PAYMENTSCREEN_LOCALE_BROWSER_LOCALE,
                            'name' => $this->module->l('No, use browser\'s Locale', 'FormBuilder'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ])
        ;

        if (Config::isVersion17()) {
            $builder
                ->add('MailSettings', LegacyMailSettingsType::class, [])
            ;
        }

        $builder
            ->add(Config::MOLLIE_KLARNA_INVOICE_ON, null, [
                'type' => 'select',
                'label' => $this->module->l('When to create the invoice?', 'FormBuilder'),
                'desc' => $this->module->display($this->module->getPathUri(), 'views/templates/admin/invoice_description.tpl'),
                'name' => Config::MOLLIE_KLARNA_INVOICE_ON,
                'options' => [
                    'query' => [
                        [
                            'id' => Config::MOLLIE_STATUS_KLARNA_AUTHORIZED,
                            'name' => $this->module->l('Accepted', 'FormBuilder'),
                        ],
                        [
                            'id' => Config::MOLLIE_STATUS_KLARNA_SHIPPED,
                            'name' => $this->module->l('Shipped', 'FormBuilder'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ])
            ->add('OrderStatuses', LegacyOrderStatusesSettingsType::class, [])
            ->add('Visual', LegacyVisualSettingsType::class, [])
            ->add('Shipment', LegacyShipmentSettingsType::class, [])
            ->add('Debug', LegacyDebugSettingsType::class, [])
        ;
    }
}