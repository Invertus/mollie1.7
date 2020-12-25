<?php

namespace Mollie\Builder\Form;

use Configuration;
use HelperForm;
use Mollie;
use Mollie\Builder\FormBuilderInterface;
use Tools;

abstract class AbstractLegacyForm implements FormParserInterface
{
    /**
     * @var Mollie
     */
    protected $module;

    /**
     * @var string
     */
    private $submitAction = '';

    /**
     * @var string
     */
    private $token = '';

    /**
     * @var array
     */
    private $fieldsValue = [];

    /**
     * @var FormBuilderInterface
     */
    private $formBuilder;

    public function __construct(Mollie $module)
    {
        $this->module = $module;
    }

    public function parse()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->module->getTable();
        $helper->module = $this->module;
        $helper->default_form_language = $this->module->getContext()->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->module->getIdentifier();
        $helper->submit_action = $this->getSubmitAction();
        $helper->token = $this->getToken();
        $helper->fields_value = $this->getFieldsValue();

        return $helper->generateForm([
            [
                'form' => [
                    'input' => $this->getFormBuilder()->build(),
                    'submit' => [
                        'title' => $this->module->l('Save'),
                        'class' => 'btn btn-default pull-right',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param string $submitAction
     */
    public function setSubmitAction($submitAction)
    {
        $this->submitAction = $submitAction;
    }

    /**
     * @return string
     */
    public function getSubmitAction()
    {
        return !empty($this->submitAction) ? $this->submitAction : sprintf('submit%sConfiguration', $this->module->name);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return !empty($this->token) ? $this->token : Tools::getAdminTokenLite(Mollie::ADMIN_MOLLIE_PARENT_CONTROLLER);
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function getFieldsValue()
    {
        return $this->fieldsValue;
    }

    /**
     * @param array $fieldsValue
     */
    public function setFieldsValue($fieldsValue)
    {
        $this->fieldsValue = $fieldsValue;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getFormBuilder()
    {
        return $this->formBuilder; //TODO maybe create fallback to null builder.
    }

    /**
     * @param FormBuilderInterface $formBuilder
     */
    public function setFormBuilder($formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }
}