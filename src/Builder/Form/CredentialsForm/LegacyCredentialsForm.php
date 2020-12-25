<?php

namespace Mollie\Builder\Form\CredentialsForm;

use Mollie\Builder\Form\AbstractLegacyForm;
use Mollie\Form\Admin\Credentials\CredentialsType;
use Mollie\Provider\Form\CredentialsForm\CredentialsFormValuesProvider;
use Mollie\Provider\Form\FormValuesProvider;

class LegacyCredentialsForm extends AbstractLegacyForm implements CredentialsFormInterface
{
    /**
     * @inheritDoc
     */
    public function parse()
    {
        /** @var FormValuesProvider $credentialsFormValuesProvider */
        $credentialsFormValuesProvider = $this->module->getMollieContainer(CredentialsFormValuesProvider::class);
        $this->setFieldsValue($credentialsFormValuesProvider->getFormValues());

        /** @var CredentialsFormBuilderInterface $credentialsFormBuilder */
        $credentialsFormBuilder = $this->module->getMollieContainer(CredentialsFormBuilderInterface::class);
        $credentialsFormBuilder->add('CredentialsForm', CredentialsType::class); //To initialize form on creation.
        $this->setFormBuilder($credentialsFormBuilder);

        return parent::parse();
    }
}