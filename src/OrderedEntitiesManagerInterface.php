<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle;

interface OrderedEntitiesManagerInterface
{
    public function getPrevious(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface;
    public function getNextEntity(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface;
}
