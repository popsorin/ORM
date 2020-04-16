<?php

declare(strict_types=1);

namespace ReallyOrm\Entity;

use ReallyOrm\Repository\RepositoryManagerInterface;

/**
 * Class AbstractEntity.
 *
 * Intended as a parent for concrete entities.
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var int
     * @MappedOn id
     */
    protected $id;

    /**
     * @var RepositoryManagerInterface
     */
    protected $repositoryManager;

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $entity): bool
    {
        return $this->repositoryManager->getRepository($entity->getEntityName())->insertOnDuplicateKeyUpdate($entity);
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): \b
    {
        return $this->repositoryManager->getRepository($entity->getEntityName())->delete($entity);
    }
}
