<?php


namespace ReallyOrm\Test\Repository;


use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Repository\RepositoryInterface;
use ReallyOrm\Repository\RepositoryManagerInterface;
use ReallyOrm\Test\Entity\User;

class RepositoryManager implements RepositoryManagerInterface
{
    /**
     * @var array
     */
    private $repositories;

    /**
     * @inheritDoc
     */
    public function getRepository(string $className): RepositoryInterface
    {
        return $this->repositories[$className];
    }

    /**
     * @inheritDoc
     */
    public function addRepository(RepositoryInterface $repository): RepositoryManagerInterface
    {
        $this->repositories[$repository->getEntityName()] = $repository;
        return $this;
    }
}