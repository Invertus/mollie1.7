<?php

namespace Mollie\Builder\Form\CredentialsForm;

use Mollie;
use Mollie\Builder\TemplateBuilderInterface;
use Mollie\Config\Config;
use Mollie\Utility\EnvironmentUtility;
use Mollie\Utility\TagsUtility;

class CredentialsForm implements TemplateBuilderInterface
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
        return [
            [
                'type' => 'mollie-support',
                'name' => '',
            ],
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
            ],
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
            ],
            [
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
            ],
            [
                'type' => 'mollie-password',
                'label' => $this->module->l('API Key Live', 'FormBuilder'),

                'name' => Config::MOLLIE_API_KEY,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-live-api-group',
            ]
        ];
    }
}