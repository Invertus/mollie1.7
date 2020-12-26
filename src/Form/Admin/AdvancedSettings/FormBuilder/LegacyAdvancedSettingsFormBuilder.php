<?php

namespace Mollie\Form\Admin\AdvancedSettings\FormBuilder;

use Mollie\Form\AbstractLegacyFormBuilder;
use Mollie\Form\Admin\AdvancedSettings\AdvancedSettingsFormInterface;
use Mollie\Form\Admin\AdvancedSettings\LegacyAdvancedSettingsForm;
use Mollie\Form\Admin\AdvancedSettings\Type\LegacyAdvancedSettingsType;
use Mollie\Form\FormInterface;

class LegacyAdvancedSettingsFormBuilder extends AbstractLegacyFormBuilder implements AdvancedSettingsFormBuilderInterface
{
    const FORM_NAME = 'AdvancedSettingsForm';

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        $this->resetBlocks(); //Hack to reset all blocks as we are unable to create FormBuilder by factories.
        $this->add(self::FORM_NAME, LegacyAdvancedSettingsType::class);

        /** @var LegacyAdvancedSettingsForm $advancedSettingsForm */
        $advancedSettingsForm = $this->module->getMollieContainer(LegacyAdvancedSettingsForm::class);
        $advancedSettingsForm->setData($this->build());

        return $advancedSettingsForm;
    }
}