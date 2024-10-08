<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle;

use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilderInterface;
use Doctrine\Persistence\ManagerRegistry;

class OrderedEntitiesManager implements OrderedEntitiesManagerInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly CriteriaBuilderInterface $criteriaBuilder
    ) {

    }

    public function getPrevious(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface
    {
        return $this->findEntity($orderedEntity, SortOrder::ASC);
    }

    public function getNextEntity(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface
    {
        return $this->findEntity($orderedEntity, SortOrder::DESC);
    }

    protected function findEntity(OrderedEntityInterface $orderedEntity, string $direction): ?OrderedEntityInterface
    {
        $className = get_class($orderedEntity);

        $criteria = $this->criteriaBuilder->build($orderedEntity, $direction);

        $resultList = $this->registry->getRepository($className)
            ->matching($criteria)
            ->getValues();

        return array_pop($resultList);
    }
}
