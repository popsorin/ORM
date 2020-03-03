<?php


namespace ReallyOrm\Test\Repository;


use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Repository\AbstractRepository;
use ReallyOrm\Test\Entity\User;

class UserRepository extends AbstractRepository
{

    public function find($int): EntityInterface
    {
        //$query = $this->pdo->prepare("SELECT * FROM User WHERE id = ?");
       // $query->execute([$int]);
      //  $row = $query->fetch();

        return $this->hydrator->hydrate(User::class, ['id' => 1, 'name' =>'nume', 'email' => 'email']);
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $filters): ?EntityInterface
    {
        // TODO: Implement findOneBy() method.
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters, array $sorts, int $from, int $size): array
    {
        // TODO: Implement findBy() method.
    }

    /**
     * @inheritDoc
     */
    public function insertOnDuplicateKeyUpdate(EntityInterface $entity): bool
    {
        // TODO: Implement insertOnDuplicateKeyUpdate() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $entity): bool
    {
        // TODO: Implement delete() method.
    }
}