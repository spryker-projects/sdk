<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Sdk\ValueResolvers;

use SprykerSdk\Sdk\Core\Appplication\Dependency\ConfigurableValueResolverInterface;

class StaticValueResolver implements ConfigurableValueResolverInterface
{
    protected mixed $value;
    protected string $alias;
    protected mixed $description;
    /**
     * @var mixed|null
     */
    protected ?string $help;
    /**
     * @var mixed|string
     */
    protected string $type;

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'STATIC';
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSettingPaths(): array
    {
        return [];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getValue(array $settingValues): mixed
    {
        return $this->value;
    }

    public function configure(array $values): void
    {
        $this->value = $values['value'];
        $this->alias = $values['name'];
        $this->description = $values['description'];
        $this->help = $values['help'] ?? null;
        $this->type = $values['type'] ?? 'string';
    }
}