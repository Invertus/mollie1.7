<?php
/**
 * Mollie       https://www.mollie.nl
 *
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 *
 * @see        https://github.com/mollie/PrestaShop
 *
 * @license     https://github.com/mollie/PrestaShop/blob/master/LICENSE.md
 * @codingStandardsIgnoreStart
 */

use Mollie\Controller\AbstractAdminController;
use Mollie\Exception\FormSettingVerificationException;
use Mollie\Form\Admin\Credentials\CredentialsFormDataProvider;
use Mollie\Form\Admin\Credentials\CredentialsFormInterface;
use Mollie\Form\Admin\Credentials\FormBuilder\CredentialsFormBuilderInterface;
use Mollie\Service\ExceptionService;
use Mollie\Verification\Form\CanSettingFormBeSaved;
use Mollie\Verification\Form\FormSettingVerification;

class AdminMollieCredentialsController extends AbstractAdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		parent::__construct();
	}

	public function initContent()
	{
		parent::initContent();

		/** @var CredentialsFormBuilderInterface $credentialsFormBuilder */
		$credentialsFormBuilder = $this->module->getMollieContainer(CredentialsFormBuilderInterface::class);

		$this->content .= $credentialsFormBuilder->getForm()->createView();

		$this->context->smarty->assign([
		    'content' => $this->content
        ]);
	}

	public function postProcess()
    {
        /** @var CredentialsFormBuilderInterface $credentialsFormBuilder */
        $credentialsFormBuilder = $this->module->getMollieContainer(CredentialsFormBuilderInterface::class);

        /** @var CredentialsFormInterface $credentialsForm */
        $credentialsForm = $credentialsFormBuilder->getForm();

        $credentialsForm->handleRequest(null);

        if ($credentialsForm->isSubmitted() && $credentialsForm->isValid()) {
            try {
                /** @var FormSettingVerification $canSettingFormBeSaved */
                $canSettingFormBeSaved = $this->module->getMollieContainer(CanSettingFormBeSaved::class);

                if ($canSettingFormBeSaved->verify()) {
                    /** @var CredentialsFormDataProvider $credentialsFormDataProvider */
                    $credentialsFormDataProvider = $this->module->getMollieContainer(CredentialsFormDataProvider::class);
                    $credentialsFormDataProvider->setData($credentialsFormDataProvider->getData());
                }
            } catch (FormSettingVerificationException $e) {
                /** @var ExceptionService $exceptionService */
                $exceptionService = $this->module->getMollieContainer(ExceptionService::class);
                $this->errors[] = $exceptionService->getErrorMessageForException(
                    $e,
                    $exceptionService->getErrorMessages()
                );

                return null;
            }
            $this->confirmations[] = $this->module->l('Successfully updated settings');

            return null;
        }

        return parent::postProcess();
    }
}
