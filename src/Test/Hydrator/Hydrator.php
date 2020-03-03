<?php

namespace ReallyOrm\Test\Hydrator;

use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Hydrator\HydratorInterface;
use ReflectionClass;
use ReflectionException;

class Hydrator implements HydratorInterface
{
    const COLUMN_NAME = 'columnName';
    const REGEX_MAPPEDON = '/@MappedOn (?<%s>\w+)/m';

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function hydrate(string $className, array $data): EntityInterface
    {
        $reflection = new ReflectionClass($className);
        $entity = $reflection->newInstanceWithoutConstructor();
        $props = $reflection->getProperties();
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $docBlock = $prop->getDocComment();
            preg_match(sprintf(self::REGEX_MAPPEDON, self::COLUMN_NAME), $docBlock, $matches);
            if(!isset($matches[self::COLUMN_NAME])) {
                continue;
            }
            if(!isset($data[$matches[self::COLUMN_NAME]])){
                continue;
            }
            $prop->setValue($entity, $data[$matches[self::COLUMN_NAME]]);
        }

        return $entity;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function extract(EntityInterface $object): array
    {
        $reflection = new ReflectionClass(get_class($object));
        $extracted = [];
        $props = $reflection->getProperties();
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $docBlock = $prop->getDocComment();
            preg_match(sprintf(self::REGEX_MAPPEDON, self::COLUMN_NAME), $docBlock, $matches);
            if(empty($matches)) {
                continue;
            }
            $extracted[$matches[self::COLUMN_NAME]] = $prop->getValue($object);
        }

        return $extracted;
    }

    public function extractId(EntityInterface $entity): ?int
    {
        $reflection = new ReflectionClass(get_class($entity));
        $props = $reflection->getProperties();
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $docBlock = $prop->getDocComment();
            preg_match(sprintf(self::REGEX_MAPPEDON, self::COLUMN_NAME), $docBlock, $matches);
            if(empty($matches)) {
                continue;
            }
            if($matches[self::COLUMN_NAME] === 'id')
                return $prop->getValue($entity);
        }

        return null;
    }
    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function hydrateId(EntityInterface $entity, int $id): void
    {
        $reflection = new ReflectionClass(get_class($entity));
        $reflectedEntity = $reflection->newInstanceWithoutConstructor();
        $props = $reflection->getProperties();
        foreach ($props as $prop) {
            if($prop->getName() !== 'id') {
                continue;
            }
            $prop->setAccessible(true);
            $prop->setValue($reflectedEntity, $id);
        }
    }
}