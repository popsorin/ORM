<?php

declare(strict_types=1);

namespace ReallyOrm\Repository;

use ReallyOrm\Entity\EntityInterface;

/**
 * Interface RepositoryManagerInterface.
 *
 * Specifies methods to be implemented by a RepositoryManager.
 */
interface RepositoryManagerInterface
{
    /**
     * Returns the repository for an entity.
     *
     * @param string $className The entity's class name.
     *
     * @return RepositoryInterface
     */
    public function getRepository(string $className): RepositoryInterface;

    /**
     * Adds a repository to the manager's internal list of repositories.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryManagerInterface
     */
    public function addRepository(RepositoryInterface $repository): RepositoryManagerInterface;
}
