<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\OrderedEntities\CriteriaBuilder;

use MalteHuebner\OrderedEntitiesBundle\OrderedEntities\OrderedEntityInterface;
use Doctrine\Common\Collections\Criteria;

interface CriteriaBuilderInterface
{
    public function build(OrderedEntityInterface $orderedEntity, string $direction): Criteria;
}