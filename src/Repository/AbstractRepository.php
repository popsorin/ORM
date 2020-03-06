<?php

namespace ReallyOrm\Repository;

use PDO;
use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Hydrator\HydratorInterface;

/**
 * Class AbstractRepository.
 *
 * Intended as a parent for entity repositories.
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * Represents a connection between PHP and a database server.
     *
     * https://www.php.net/manual/en/class.pdo.php
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The name of the entity associated with the repository.
     *
     * This could be used, for example, to infer the underlying table name.
     *
     * @var string
     */
    protected $entityName;

    /**
     * The hydrator is used in the following two cases:
     * - build an entity from a database row
     * - extract entity fields into an array representation that is easier to use when building insert/update statements.
     *
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * AbstractRepository constructor.
     *
     * @param PDO $pdo
     * @param string $entityName
     * @param HydratorInterface $hydrator
     */
    public function __construct(PDO $pdo, string $entityName, HydratorInterface $hydrator, string $tableName)
    {
        $this->pdo = $pdo;
        $this->entityName = $entityName;
        $this->hydrator = $hydrator;
        $this->tableName = $tableName;
    }

    /**
     * Returns the name of the associated entity.
     *
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }


    public function find($int): EntityInterface
    {
        $query = $this->pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $query->bindParam("id", $int);
        $query->execute();

        return $this->hydrator->hydrate($this->getEntityName(), $query->fetch());
    }

    /**
     *
     * @inheritDoc
     */
    public function findOneBy(array $filters): ?EntityInterface
    {
        $select = "SELECT * FROM $this->tableName";

        if (!empty($filters)) {
            $select .= " WHERE ";

            foreach ($filters as $key => $filter) {
                $select .= "$key = :$key AND ";
            }
            $select = substr($select, 0, strlen($select) - 4);
        }
        $select .= "LIMIT 1;";
        $query = $this->pdo->prepare($select);

        foreach ($filters as $key => &$filter) {
            $query->bindParam(":$key", $filter);
        }

        $query->execute();
        $row = $query->fetch();
        if ($row === false) {
            throw new \Exception("User not found,sorry");
        }

        return $this->hydrator->hydrate($this->getEntityName(), $row);
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $filters, array $sorts, int $from, int $size): array
    {
        $select = "SELECT * FROM $this->tableName";

        if (!empty($filters)) {
            $select .= " WHERE ";

            foreach ($filters as $key => $filter) {
                $select .= "$key = :$key AND ";
            }
            $select = substr($select, 0, strlen($select) - 4);
        }
        if(!empty($sorts)){
            $select .= " ORDER BY ";

            foreach ($sorts as $key => $direction) {
                if (strcmp($direction, "ASC")!==0 && strcmp($direction, "DESC")!==0){
                    continue;
                }
                $select .= " $key $direction , ";
            }
            $select = substr($select, 0, -2);
        }

        if($size !== null || $size !== 0) {
            $select .= "LIMIT $size ";
            if($from !== null || $from !== 0) {
                $select .= "OFFSET $from ";
            }
        }
        $select .= ";";

        $query = $this->pdo->prepare($select);

        foreach ($filters as $key => &$filter) {
            $query->bindParam(":$key", $filter);
        }
        $query->execute();

        $arrayFound = [];
        while($row = $query->fetch()){
            array_push($arrayFound, $this->hydrator->hydrate($this->getEntityName(), $row));
        }

        return $arrayFound;
    }

    /**
     * @inheritDoc
     */
    public function insertOnDuplicateKeyUpdate(EntityInterface $entity): bool
    {
        $extractedEntity = $this->hydrator->extract($entity);
        $query = $this->ifExists($extractedEntity);

        if ($query === false) {
            return self::insert($extractedEntity);
        }
        return self::update($extractedEntity);
    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $entity): bool
    {
        $id = $this->hydrator->extractId($entity);
        if($id === null) {
            return false;
        }
        $query = $this->pdo->prepare("DELETE FROM user_table WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->rowCount() > 0;
    }

    /**
     * takes the values from the input array and inserts them into the database
     * @param array $extractedEntity
     * @return bool
     */
    public function insert(array $extractedEntity)
    {
        $insert = "INSERT INTO $this->tableName (";
        foreach ($extractedEntity as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            $insert .= " $key,";
        }
        $insert = substr($insert, 0, -1);
        $insert .= ") VALUES (";
        foreach ($extractedEntity as $key => &$value) {
            $insert .= " :$value,";
        }
        $insert = substr($insert, 0, -5);
        $insert .= ");";
        $query = $this->pdo->prepare($insert);
        foreach ($extractedEntity as $key => &$value) {
            if ($key === 'id') {
                continue;
            }
            $query->bindParam(":$value", $value);
        }

        return $query->execute();
    }

    public function update(array $extractedEntity)
    {
        $insert = "UPDATE $this->tableName SET ";
        foreach ($extractedEntity as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            $insert .= " $key=:$key,";
        }
        $insert = substr($insert, 0, -1);
        $insert .= " WHERE id=:id;";
        $query = $this->pdo->prepare($insert);
        foreach ($extractedEntity as $key => &$value) {
            $query->bindParam(":$key", $value);
        }
        return $query->execute();
    }

    public function ifExists(array $extractedEntity)
    {
        $query = $this->pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $query->bindParam(":id", $extractedEntity['id']);
        $query->execute();

        return $query->fetch();
    }
}
