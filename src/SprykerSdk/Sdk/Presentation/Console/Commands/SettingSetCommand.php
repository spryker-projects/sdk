<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Sdk\Presentation\Console\Commands;

use SprykerSdk\Sdk\Core\Appplication\Dependency\ProjectSettingRepositoryInterface;
use SprykerSdk\Sdk\Core\Appplication\Exception\MissingSettingException;
use SprykerSdk\Sdk\Core\Appplication\Service\ProjectSettingManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SettingSetCommand extends Command
{
    /**
     * @var string
     */
    protected const NAME = 'setting:set';

    /**
     * @var string
     */
    protected const ARG_SETTING_PATH = 'setting_path';

    /**
     * @var string
     */
    protected const ARG_SETTING_VALUE = 'value';

    /**
     */
    public function __construct(
        protected ProjectSettingRepositoryInterface $settingRepository,
        protected ProjectSettingManager $settingManager
    ) {
        parent::__construct(static::NAME);
    }

    /**
     * @return string
     */
    public function getHelp(): string
    {
        $help = 'Setting paths: ' . PHP_EOL;

        foreach ($this->settingRepository->findProjectSettings() as $projectSetting) {
            $help .= sprintf(
                '    %s <%s> [%s] -- %s %s',
                $projectSetting->path,
                $projectSetting->type,
                $projectSetting->strategy,
                PHP_EOL . str_repeat(' ', 8),
                $projectSetting->initializationDescription
            ) . PHP_EOL;
        }

        return $help;
    }


    protected function configure()
    {
        parent::configure();
        $this->addArgument(static::ARG_SETTING_PATH, InputArgument::REQUIRED);
        $this->addArgument(static::ARG_SETTING_VALUE, InputArgument::REQUIRED);
    }


    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument(static::ARG_SETTING_PATH);

        try {
            $this->settingManager->setSetting($path, $input->getArgument(static::ARG_SETTING_VALUE));
        } catch (MissingSettingException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return static::FAILURE;
        }

        return static::SUCCESS;
    }
}