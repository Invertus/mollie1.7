<?php

namespace Mollie\Form\Admin\Credentials\FormBuilder;

use Mollie\Form\AbstractLegacyFormBuilder;
use Mollie\Form\Admin\Credentials\LegacyCredentialsForm;
use Mollie\Form\Admin\Credentials\Type\LegacyCredentialsType;
use Mollie\Form\FormInterface;

class LegacyCredentialsFormBuilder extends AbstractLegacyFormBuilder implements CredentialsFormBuilderInterface
{
	const FORM_NAME = 'CredentialsForm';

	/**
	 * @return FormInterface
	 */
	public function getForm()
	{
		$this->resetBlocks(); //Hack to reset all blocks as we are unable to create FormBuilder by factories.
		$this->add(self::FORM_NAME, LegacyCredentialsType::class);

		/** @var LegacyCredentialsForm $credentialsForm */
		$credentialsForm = $this->module->getMollieContainer(LegacyCredentialsForm::class);
		$credentialsForm->setData($this->build());

		return $credentialsForm;
	}
}
