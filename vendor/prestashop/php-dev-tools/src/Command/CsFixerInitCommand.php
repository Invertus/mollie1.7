<?php

namespace test\PrestaShop\CodingStandards\Command;

use test\Symfony\Component\Console\Input\InputInterface;
use test\Symfony\Component\Console\Input\InputOption;
use test\Symfony\Component\Console\Output\OutputInterface;
use test\Symfony\Component\Filesystem\Filesystem;
class CsFixerInitCommand extends \test\PrestaShop\CodingStandards\Command\AbstractCommand
{
    protected function configure()
    {
        $this->setName('cs-fixer:init')->setDescription('Initialize Cs Fixer environement')->addOption('dest', null, \test\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Where the configuration will be stored', '.');
    }
    protected function execute(\test\Symfony\Component\Console\Input\InputInterface $input, \test\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $fs = new \test\Symfony\Component\Filesystem\Filesystem();
        $directory = __DIR__ . '/../../templates/cs-fixer/';
        $destination = $input->getOption('dest');
        foreach (['php_cs.dist', 'prettyci.composer.json'] as $template) {
            $this->copyFile($input, $output, $directory . $template, $destination . '/.' . $template);
        }
    }
}
