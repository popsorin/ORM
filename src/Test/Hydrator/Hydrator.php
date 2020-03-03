<?php

namespace ReallyOrm\Test\Hydrator;

use ReallyOrm\Entity\EntityInterface;
use ReallyOrm\Hydrator\HydratorInterface;
use ReflectionClass;
use ReflectionException;

class Hydrator implements HydratorInterface
{
    const COLUMN_NAME = 'columnName';

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
            //tine regexu in constanta
            preg_match(sprintf('/@MappedOn (?<%s>\w+)/m', self::COLUMN_NAME), $docBlock, $matches);
            //var_dump($docBlock);
            //var_dump($matches);
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
            //tine regexu in constanta
            preg_match(sprintf('/@MappedOn (?<%s>\w+)/m', self::COLUMN_NAME), $docBlock, $matches);
     //       $extracted[$matches[self::COLUMN_NAME]] =
        }
    }

    /**
     * @inheritDoc
     */
    public function hydrateId(EntityInterface $entity, int $id): void
    {
        // TODO: Implement hydrateId() method.
    }
}