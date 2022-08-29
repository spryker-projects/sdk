<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Sdk\Infrastructure\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SprykerSdk\Sdk\Core\Application\Dependency\Repository\WorkflowRepositoryInterface;
use SprykerSdk\Sdk\Infrastructure\Entity\Workflow;
use SprykerSdk\SdkContracts\Entity\WorkflowInterface;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<\SprykerSdk\Sdk\Infrastructure\Entity\Workflow>
 */
class WorkflowRepository extends ServiceEntityRepository implements WorkflowRepositoryInterface
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workflow::class);
    }

    /**
     * @param \SprykerSdk\SdkContracts\Entity\WorkflowInterface $workflow
     *
     * @return \SprykerSdk\SdkContracts\Entity\WorkflowInterface
     */
    public function save(WorkflowInterface $workflow): WorkflowInterface
    {
        $this->getEntityManager()->persist($workflow);
        $this->getEntityManager()->flush();

        return $workflow;
    }

    /**
     * @param \SprykerSdk\SdkContracts\Entity\WorkflowInterface $workflow
     *
     * @return \SprykerSdk\SdkContracts\Entity\WorkflowInterface
     */
    public function remove(WorkflowInterface $workflow): WorkflowInterface
    {
        $this->getEntityManager()->remove($workflow);
        $this->getEntityManager()->flush();

        return $workflow;
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $project
     *
     * @return array<\SprykerSdk\SdkContracts\Entity\WorkflowInterface>
     */
    public function getWorkflows(string $project): array
    {
        $criteria = [
            'project' => $project,
            'parent' => null,
        ];

        return $this->findBy($criteria);
    }

    /**
     * @param string $project
     * @param string|null $workflowName
     *
     * @return \SprykerSdk\SdkContracts\Entity\WorkflowInterface|null
     */
    public function findWorkflow(string $project, ?string $workflowName = null): ?WorkflowInterface
    {
        $criteria = [
            'project' => $project,
        ];

        if ($workflowName) {
            $criteria['code'] = $workflowName;
        }

        return $this->findOneBy($criteria);
    }

    /**
     * @param string $project
     *
     * @return bool
     */
    public function hasWorkflow(string $project): bool
    {
        $criteria = [
            'project' => $project,
        ];

        return (bool)$this->findOneBy($criteria);
    }
}