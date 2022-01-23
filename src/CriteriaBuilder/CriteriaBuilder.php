<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder;

use MalteHuebner\OrderedEntitiesBundle\Annotation\AbstractAnnotation;
use MalteHuebner\OrderedEntitiesBundle\Annotation\Boolean;
use MalteHuebner\OrderedEntitiesBundle\Annotation\Identical;
use MalteHuebner\OrderedEntitiesBundle\Annotation\Order;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;
use MalteHuebner\OrderedEntitiesBundle\SortOrder;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\Criteria;

class CriteriaBuilder implements CriteriaBuilderInterface
{
    protected Reader $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function build(OrderedEntityInterface $orderedEntity, string $direction): Criteria
    {
        $criteria = Criteria::create();

        $criteria = $this->handleAnnotations($orderedEntity, $criteria, $direction);

        return $criteria;
    }

    protected function handleAnnotations(OrderedEntityInterface $orderedEntity, Criteria $criteria, string $direction): Criteria
    {
        $reflectionClass = new \ReflectionClass($orderedEntity);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $annotations = $this->annotationReader->getPropertyAnnotations($property);

            /** @var AbstractAnnotation $parameterAnnotation */
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Order) {
                    $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                    $compareMethodName = $direction === SortOrder::ASC ? 'lt' : 'gt';

                    $criteria
                        ->orderBy([$property->getName() => $direction])
                        ->andWhere(Criteria::expr()->$compareMethodName($property->getName(), $orderedEntity->$getMethodName()));
                }

                if ($annotation instanceof Identical) {
                    $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                    $criteria->andWhere(Criteria::expr()->eq($property->getName(), $orderedEntity->$getMethodName()));
                }

                if ($annotation instanceof Boolean) {
                    $criteria->andWhere(Criteria::expr()->eq($property->getName(), $annotation->getValue()));
                }
            }
        }

        return $criteria;
    }
}
