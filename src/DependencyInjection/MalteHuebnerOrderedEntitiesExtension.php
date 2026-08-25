<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\DependencyInjection;

use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilder;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntitiesManager;
use MalteHuebner\OrderedEntitiesBundle\TwigExtension\OrderedEntityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\Extension;

class MalteHuebnerOrderedEntitiesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $criteriaBuilder = new Definition(CriteriaBuilder::class);
        $criteriaBuilder->setPublic(true);
        $container->setDefinition(CriteriaBuilder::class, $criteriaBuilder);

        $orderedEntitiesManager = new Definition(OrderedEntitiesManager::class);
        $orderedEntitiesManager->setPublic(true);
        $orderedEntitiesManager->setArguments([
            new Reference('doctrine'),
            new Reference(CriteriaBuilder::class),
        ]);
        $container->setDefinition(OrderedEntitiesManager::class, $orderedEntitiesManager);

        $twigExtension = new Definition(OrderedEntityExtension::class);
        $twigExtension->setPublic(true);
        $twigExtension->addTag('twig.extension');
        $twigExtension->setArguments([
            new Reference(OrderedEntitiesManager::class),
        ]);
        $container->setDefinition(OrderedEntityExtension::class, $twigExtension);
    }
}
