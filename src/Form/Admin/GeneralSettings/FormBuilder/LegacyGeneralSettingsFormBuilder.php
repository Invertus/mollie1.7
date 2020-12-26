<?php

namespace Mollie\Form\Admin\GeneralSettings\FormBuilder;

use Mollie\Form\AbstractLegacyFormBuilder;
use Mollie\Form\Admin\GeneralSettings\GeneralSettingsFormInterface;
use Mollie\Form\Admin\GeneralSettings\Type\GeneralSettingsTypeInterface;
use Mollie\Form\FormInterface;

class LegacyGeneralSettingsFormBuilder extends AbstractLegacyFormBuilder implements GeneralSettingsFormBuilderInterface
{
    const FORM_NAME = 'GeneralSettingsForm';

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        $this->resetBlocks(); //Hack to reset all blocks as we are unable to create FormBuilder by factories.
        $this->add(self::FORM_NAME, GeneralSettingsTypeInterface::class);

        /** @var GeneralSettingsFormInterface $generalSettingsForm */
        $generalSettingsForm = $this->module->getMollieContainer(GeneralSettingsFormInterface::class);
        $generalSettingsForm->setData($this->build());

        return $generalSettingsForm;
    }
}