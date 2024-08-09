<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\TwigExtension;

use MalteHuebner\OrderedEntitiesBundle\OrderedEntitiesManagerInterface;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OrderedEntityExtension extends AbstractExtension
{
    public function __construct(
        private readonly OrderedEntitiesManagerInterface $orderedEntitiesManager
    ) {

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('previous_entity', [$this, 'previousEntity'], ['is_safe' => ['html']]),
            new TwigFunction('next_entity', [$this, 'nextEntity'], ['is_safe' => ['html']]),
        ];
    }

    public function previousEntity(OrderedEntityInterface $entity): ?OrderedEntityInterface
    {
        return $this->orderedEntitiesManager->getPrevious($entity);
    }


    public function nextEntity(OrderedEntityInterface $entity): ?OrderedEntityInterface
    {
        return $this->orderedEntitiesManager->getNextEntity($entity);
    }

    public function getName(): string
    {
        return 'ordered_entity_extension';
    }
}
