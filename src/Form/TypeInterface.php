<?php

namespace Mollie\Form;

interface TypeInterface
{
    /**
     * TODO implement option override when called with Type.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return mixed
     */
    public function buildForm(FormBuilderInterface $builder, array $options);
}