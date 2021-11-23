<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Sdk\Infrastructure\Repository;

use SplFileInfo;
use SprykerSdk\Sdk\Core\Appplication\Exception\MissingSettingException;
use SprykerSdk\Sdk\Core\Domain\Entity\Command;
use SprykerSdk\Sdk\Core\Domain\Entity\Placeholder;
use SprykerSdk\Sdk\Core\Domain\Entity\Task;
use SprykerSdk\Sdk\Core\Domain\Entity\TaskInterface;
use SprykerSdk\Sdk\Core\Domain\Repository\SettingRepositoryInterface;
use SprykerSdk\Sdk\Core\Domain\Repository\TaskRepositoryInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class TaskYamlRepository implements TaskRepositoryInterface
{
    /**
     * @param \SprykerSdk\Sdk\Core\Domain\Repository\SettingRepositoryInterface $settingRepository
     * @param \Symfony\Component\Finder\Finder $fileFinder
     * @param \Symfony\Component\Yaml\Yaml $yamlParser
     * @param iterable<\SprykerSdk\Sdk\Core\Domain\Entity\TaskInterface> $existingTasks
     */
    public function __construct(
        protected SettingRepositoryInterface $settingRepository,
        protected Finder $fileFinder,
        protected Yaml $yamlParser,
        protected iterable $existingTasks = [],
    ) {
    }

    /**
     * @param array $tags
     *
     * @return array
     */
    public function findAll(array $tags = []): array
    {
        $taskDirSetting = $this->settingRepository->findOneByPath('task_dirs');

        if (!$taskDirSetting || !is_array($taskDirSetting->getValues())){
            throw new MissingSettingException('task_dirs are not configured properly');
        }

        $tasks = [];
        $taskListData = [];
        //read task from path, parse and create Task, later use DB for querying
        foreach ($this->fileFinder->in($taskDirSetting->getValues())->name('*.yaml')->files() as $taskFile) {
            $taskData = $this->yamlParser->parse($taskFile->getContents());
            $taskListData[$taskData['id']] = $taskData;
        }

        foreach ($taskListData as $taskData) {
            $task = $this->buildTask($taskData, $taskListData, $tags);
            $tasks[$task->getId()] = $task;
        }

        foreach ($this->existingTasks as $existingTask) {
            $tasks[$existingTask->getId()] = $existingTask;
        }

        return $tasks;
    }

    /**
     * @param string $taskId
     * @param array $tags
     *
     * @return \SprykerSdk\Sdk\Core\Domain\Entity\TaskInterface|null
     */
    public function findById(string $taskId, array $tags = []): ?TaskInterface
    {
        $tasks = $this->findAll($tags);

        if (array_key_exists($taskId, $tasks)) {
            return $tasks[$taskId];
        }

        return null;
    }

    /**
     * @param array $data
     * @param array $taskListData
     * @param array $tags
     *
     * @return array<string, \SprykerSdk\Sdk\Core\Domain\Entity\Placeholder>
     */
    protected function buildPlaceholders(array $data, array $taskListData, array $tags = []): array
    {
        $placeholders = [];
        $taskPlaceholders = [];
        $taskPlaceholders[] = $data['placeholders'] ?? [];

        if ($data['type'] === 'task_set') {
            foreach ($data['tasks'] as $task) {
                if ($tags && !array_intersect($tags, $task['tags'])) {
                    continue;
                }
                $taskPlaceholders[] = $taskListData[$task['id']]['placeholders'];
            }
        }
        $taskPlaceholders = array_merge(...$taskPlaceholders);

        foreach ($taskPlaceholders as $placeholderData) {
            $placeholderName = $placeholderData['name'];
            $placeholders[$placeholderName] = new Placeholder(
                $placeholderName,
                $placeholderData['value_resolver'],
                $placeholderData['configuration'] ?? [],
                $placeholderData['optional'] ?? false,
            );
        }

        return $placeholders;
    }

    /**
     * @param array $data
     * @param array $taskListData
     * @param array<string> $taskListData
     *
     * @return array<int, \SprykerSdk\Sdk\Core\Domain\Entity\Command>
     */
    protected function buildCommands(array $data, array $taskListData, array $tags = []): array
    {
        $commands = [];

        if ($data['type'] === 'local_cli') {
            $commands[] = new Command(
                $data['command'],
                $data['type'],
                true,
            );
        }

        if ($data['type'] === 'task_set') {
            foreach ($data['tasks'] as $task) {
                if ($tags && !array_intersect($tags, $task['tags'])) {
                    continue;
                }
                $commands[] = new Command(
                    $taskListData[$task['id']]['command'],
                    $taskListData[$task['id']]['type'],
                    $task['stop_on_error'],
                    $task['tags']
                );
            }
        }

        return $commands;
    }

    /**
     * @param array $taskData
     * @param array $taskListData
     * @param array $tags
     *
     * @return \SprykerSdk\Sdk\Core\Domain\Entity\TaskInterface
     */
    protected function buildTask(array $taskData, array $taskListData, array $tags = []): TaskInterface
    {
        $placeholders = $this->buildPlaceholders($taskData, $taskListData, $tags);
        $commands = $this->buildCommands($taskData, $taskListData, $tags);

        return new Task(
            $taskData['id'],
            $taskData['short_description'],
            $commands,
            $placeholders,
            $taskData['help'] ?? null,
        );
    }
}
