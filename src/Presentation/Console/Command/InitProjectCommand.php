<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Sdk\Presentation\Console\Command;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Psr\Container\ContainerInterface;
use SprykerSdk\Sdk\Core\Application\Dependency\Repository\SettingRepositoryInterface;
use SprykerSdk\Sdk\Core\Application\Dto\ReceiverValue;
use SprykerSdk\Sdk\Core\Application\Service\SettingManager;
use SprykerSdk\Sdk\Extension\Dependency\Setting\SettingChoicesProviderInterface;
use SprykerSdk\Sdk\Infrastructure\Service\CliValueReceiver;
use SprykerSdk\SdkContracts\Entity\SettingInterface;
use SprykerSdk\SdkContracts\Setting\SettingInitializerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitProjectCommand extends Command
{
    /**
     * @var string
     */
    protected const NAME = 'sdk:init:project';

    /**
     * @var \SprykerSdk\Sdk\Infrastructure\Service\CliValueReceiver
     */
    protected CliValueReceiver $cliValueReceiver;

    /**
     * @var \SprykerSdk\Sdk\Core\Application\Service\SettingManager
     */
    protected SettingManager $projectSettingManager;

    /**
     * @var \SprykerSdk\Sdk\Core\Application\Dependency\Repository\SettingRepositoryInterface
     */
    protected SettingRepositoryInterface $settingRepository;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var string
     */
    protected string $projectSettingFileName;

    /**
     * @param \SprykerSdk\Sdk\Infrastructure\Service\CliValueReceiver $cliValueReceiver
     * @param \SprykerSdk\Sdk\Core\Application\Service\SettingManager $projectSettingManager
     * @param \SprykerSdk\Sdk\Core\Application\Dependency\Repository\SettingRepositoryInterface $settingRepository
     * @param \Psr\Container\ContainerInterface $container
     * @param string $projectSettingFileName
     */
    public function __construct(
        CliValueReceiver $cliValueReceiver,
        SettingManager $projectSettingManager,
        SettingRepositoryInterface $settingRepository,
        ContainerInterface $container,
        string $projectSettingFileName
    ) {
        $this->projectSettingFileName = $projectSettingFileName;
        $this->settingRepository = $settingRepository;
        $this->projectSettingManager = $projectSettingManager;
        $this->cliValueReceiver = $cliValueReceiver;
        $this->container = $container;
        parent::__construct(static::NAME);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'default',
            'd',
            InputOption::VALUE_NONE,
            'Use predefined settings without approve',
        );
        try {
            $settings = $this->settingRepository->findProjectSettings();
        } catch (TableNotFoundException $e) {
            $this->setHidden(true);

            return;
        }

        foreach ($settings as $setting) {
            $mode = InputOption::VALUE_REQUIRED;
            if ($setting->getStrategy() === 'merge') {
                $mode = $mode | InputOption::VALUE_IS_ARRAY;
            }
            $this->addOption(
                $setting->getPath(),
                null,
                $mode,
                (string)$setting->getInitializationDescription(),
            );
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (file_exists($this->projectSettingFileName)) {
            if (
                !$this->cliValueReceiver->receiveValue(
                    new ReceiverValue('Project settings file already exists, should it be overwritten?', false, 'boolean'),
                )
            ) {
                return static::SUCCESS;
            }
        }

        $settingEntities = $this->settingRepository->findProjectSettings();

        $needsToAsk = (bool)$input->getOption('default');
        $settingEntities = $this->initializeSettingValues($input->getOptions(), $settingEntities, $needsToAsk);
        $this->writeProjectSettings($settingEntities);
        $this->createGitignore();

        return static::SUCCESS;
    }

    /**
     * @param array $options
     * @param array<string, \SprykerSdk\SdkContracts\Entity\SettingInterface> $settingEntities
     * @param bool $needsToAsk
     *
     * @return array<\SprykerSdk\SdkContracts\Entity\SettingInterface>
     */
    protected function initializeSettingValues(array $options, array $settingEntities, bool $needsToAsk): array
    {
        $settingEntitiesToSave = [];
        foreach ($settingEntities as $settingEntity) {
            if (
                (empty($options[$settingEntity->getPath()]) && $settingEntity->hasInitialization() === false) ||
                ($settingEntity->getInitializer() && in_array($settingEntity->getType(), ['uuid']))
            ) {
                continue;
            }
            $values = $options[$settingEntity->getPath()] ?? $settingEntity->getValues();

            if (empty($options[$settingEntity->getPath()])) {
                $needsToAsk = !$this->cliValueReceiver->receiveValue(
                    new ReceiverValue(
                        sprintf('Would you like to change the default value for `%s` setting?', $settingEntity->getPath()),
                        false,
                        'boolean',
                    ),
                );
            }

            if (!$needsToAsk && !$options[$settingEntity->getPath()]) {
                $questionDescription = $settingEntity->getInitializationDescription();

                if (!$questionDescription) {
                    $questionDescription = 'Initial value for ' . $settingEntity->getPath();
                }

                $choiceValues = [];
                $initializer = $this->getSettingInitializer($settingEntity);
                if ($initializer instanceof SettingChoicesProviderInterface) {
                    $choiceValues = $initializer->getChoices($settingEntity);
                }

                $values = $this->cliValueReceiver->receiveValue(
                    new ReceiverValue(
                        $questionDescription,
                        is_array($values) ? array_key_first($values) : $values,
                        $settingEntity->getType(),
                        $choiceValues,
                    ),
                );
            }
            $values = ['boolean' => (bool)$values, 'array' => (array)$values][$settingEntity->getType()] ?? (string)$values;
            if ($settingEntity->getType() !== 'array' && $values === $settingEntity->getValues()) {
                continue;
            }
            $settingEntity->setValues($values);
            $settingEntitiesToSave[] = $settingEntity;
        }

        foreach ($settingEntities as $settingEntity) {
            $initializer = $this->getSettingInitializer($settingEntity);
            if ($initializer) {
                $initializer->initialize($settingEntity);
            }
        }

        return $settingEntitiesToSave;
    }

    /**
     * @param array<int, \SprykerSdk\SdkContracts\Entity\SettingInterface> $projectSettings
     *
     * @return void
     */
    protected function writeProjectSettings(array $projectSettings): void
    {
        $projectValues = [];

        foreach ($projectSettings as $projectSetting) {
            $projectValues[$projectSetting->getPath()] = $projectSetting->getValues();
        }

        $this->projectSettingManager->setSettings($projectValues);
    }

    /**
     * @param \SprykerSdk\SdkContracts\Entity\SettingInterface $setting
     *
     * @return \SprykerSdk\SdkContracts\Setting\SettingInitializerInterface|null
     */
    protected function getSettingInitializer(SettingInterface $setting): ?SettingInitializerInterface
    {
        $initializerId = $setting->getInitializer() ?? '';

        if (!$this->container->has($initializerId)) {
            return null;
        }

        $initializer = $this->container->get($initializerId);
        if (!$initializer instanceof SettingInitializerInterface) {
            return null;
        }

        return $initializer;
    }

    /**
     * @return void
     */
    protected function createGitignore(): void
    {
        $settingsDir = dirname($this->projectSettingFileName);

        if (realpath($settingsDir) !== realpath('.')) {
            file_put_contents(sprintf('%s/.gitignore', $settingsDir), '*');
        }
    }
}