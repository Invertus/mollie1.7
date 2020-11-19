<?php

namespace test\PrestaShop\CodingStandards\Command;

use test\Symfony\Component\Console\Input\InputInterface;
use test\Symfony\Component\Console\Input\InputOption;
use test\Symfony\Component\Console\Output\OutputInterface;
use test\Symfony\Component\Filesystem\Filesystem;
class PhpStanInitCommand extends \test\PrestaShop\CodingStandards\Command\AbstractCommand
{
    protected function configure()
    {
        $this->setName('phpstan:init')->setDescription('Initialize phpstan environement')->addOption('dest', null, \test\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Where the configuration will be stored', 'tests/phpstan');
    }
    protected function execute(\test\Symfony\Component\Console\Input\InputInterface $input, \test\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $fs = new \test\Symfony\Component\Filesystem\Filesystem();
        $directory = __DIR__ . '/../../templates/phpstan/';
        $destination = $input->getOption('dest');
        foreach (['phpstan.neon'] as $template) {
            $this->copyFile($input, $output, $directory . $template, $destination . '/' . $template);
        }
    }
}
