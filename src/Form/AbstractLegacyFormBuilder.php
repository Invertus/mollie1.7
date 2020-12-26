<?php

namespace Mollie\Form;

use Mollie;

abstract class AbstractLegacyFormBuilder implements FormBuilderInterface
{
    /**
     * @var array
     */
    protected $blocks = [];

    /**
     * @var Mollie
     */
    protected $module;

    public function __construct(Mollie $module)
    {
        $this->module = $module;
    }

    /**
     * @inheritDoc
     */
    public function add($child, $type = null, array $options = [])
    {
        if ($type) {
            /** @var TypeInterface $typeBlock */
            $typeBlock = $this->module->getMollieContainer($type);

            if ($typeBlock) {
                $this->blocks[] = $typeBlock->buildForm($this, $options);

                return $this;
            }
        }
        $this->blocks[$child] = $options;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build()
    {
        return $this->blocks;
    }

    public function resetBlocks()
    {
        $this->blocks = [];
    }
}