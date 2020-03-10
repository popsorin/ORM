<?php

declare(strict_types=1);

namespace ReallyOrm\Entity;

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
     * @param int $id
     */
    public function setId(int $id): void
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
}
