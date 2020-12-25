<?php

namespace Mollie\Form\Admin\Credentials;

use Mollie\Builder\FormBuilderInterface;
use Mollie\Builder\LegacyTranslatorAwareType;
use Mollie\Builder\TypeInterface;
use Mollie\Config\Config;
use Mollie\Utility\EnvironmentUtility;

class CredentialsType extends LegacyTranslatorAwareType implements TypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Information-top' , null, [
                'type' => 'mollie-support',
                'name' => '',
            ])
        ;

        if (!EnvironmentUtility::getApiKey()) {

            $builder
                ->add(Config::MOLLIE_ACCOUNT_SWITCH, null, [
                    'type' => 'mollie-switch',
                    'label' => $this->trans('Do you already have a Mollie account?', 'FormBuilder'),
                    'name' => Config::MOLLIE_ACCOUNT_SWITCH,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->trans('Enabled', 'FormBuilder'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->trans('Disabled', 'FormBuilder'),
                        ],
                    ],
//                    'desc' => $this->module->display(
//                        $this->module->getPathUri(), 'views/templates/admin/create_new_account_link.tpl'
//                    ),
                ])
            ;
        }

        $builder
            ->add(Config::MOLLIE_ENVIRONMENT, null, [
                'type' => 'select',
                'label' => $this->trans('Environment', 'FormBuilder'),
                'name' => Config::MOLLIE_ENVIRONMENT,
                'options' => [
                    'query' => [
                        [
                            'id' => Config::ENVIRONMENT_TEST,
                            'name' => $this->trans('Test', 'FormBuilder'),
                        ],
                        [
                            'id' => Config::ENVIRONMENT_LIVE,
                            'name' => $this->trans('Live', 'FormBuilder'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ])
            ->add(Config::MOLLIE_API_KEY_TEST, null, [
                'type' => 'mollie-password',
                'label' => $this->trans('API Key Test', 'FormBuilder'),
//                'desc' => TagsUtility::ppTags(
//                    $this->trans('You can find your API key in your [1]Mollie Profile[/1]; it starts with test or live.', 'FormBuilder'),
//                    [$this->module->display($this->module->getPathUri(), 'views/templates/admin/profile.tpl')]
//                ),
                'name' => Config::MOLLIE_API_KEY_TEST,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-test-api-group',
            ])
            ->add(Config::MOLLIE_API_KEY, null, [
                'type' => 'mollie-password',
                'label' => $this->trans('Profile ID', 'FormBuilder'),
//                'desc' => TagsUtility::ppTags(
//                    $this->trans('You can find your Profile ID in your [1]Mollie Profile[/1]', 'FormBuilder'),
//                    [$this->module->display($this->module->getPathUri(), 'views/templates/admin/profile.tpl')]
//                ),
                'name' => Config::MOLLIE_PROFILE_ID,
                'required' => true,
                'class' => 'fixed-width-xxl',
                'form_group_class' => 'js-api-profile-id',
            ])
        ;

        if (EnvironmentUtility::getApiKey()) {
            $builder
                ->add(Config::MOLLIE_API_KEY_TESTING_BUTTON, null, [
                    'type' => 'mollie-button',
                    'label' => '',
                    'name' => Config::MOLLIE_API_KEY_TESTING_BUTTON,
                    'text' => $this->trans('Test ApiKey', 'FormBuilder'),
                    'class' => 'js-test-api-keys',
                    'form_group_class' => 'js-api-key-test',
                ])
            ;
        }

        $builder
            ->add('Information-footer', null, [
                'type' => 'mollie-h3',
                'name' => '',
                'title' => '',
            ])
        ;
    }
}