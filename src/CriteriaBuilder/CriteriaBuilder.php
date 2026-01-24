<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder;

use MalteHuebner\OrderedEntitiesBundle\Attribute\Boolean;
use MalteHuebner\OrderedEntitiesBundle\Attribute\Identical;
use MalteHuebner\OrderedEntitiesBundle\Attribute\Order;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;
use MalteHuebner\OrderedEntitiesBundle\SortOrder;
use Doctrine\Common\Collections\Criteria;

class CriteriaBuilder implements CriteriaBuilderInterface
{
    public function build(OrderedEntityInterface $orderedEntity, string $direction): Criteria
    {
        $criteria = Criteria::create();

        $criteria = $this->handleAttributes($orderedEntity, $criteria, $direction);

        return $criteria;
    }

    protected function handleAttributes(OrderedEntityInterface $orderedEntity, Criteria $criteria, string $direction): Criteria
    {
        $reflectionClass = new \ReflectionClass($orderedEntity);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes();

            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();

                if ($attributeInstance instanceof Order) {
                    $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                    $compareMethodName = $direction === SortOrder::ASC ? 'lt' : 'gt';

                    $criteria
                        ->orderBy([$property->getName() => $direction])
                        ->andWhere(Criteria::expr()->$compareMethodName($property->getName(), $orderedEntity->$getMethodName()));
                }

                if ($attributeInstance instanceof Identical) {
                    $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                    $criteria->andWhere(Criteria::expr()->eq($property->getName(), $orderedEntity->$getMethodName()));
                }

                if ($attributeInstance instanceof Boolean) {
                    $criteria->andWhere(Criteria::expr()->eq($property->getName(), $attributeInstance->getValue()));
                }
            }
        }

        return $criteria;
    }
}
