<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\TwigExtension;

use MalteHuebner\OrderedEntitiesBundle\OrderedEntitiesManagerInterface;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\SimpleOrderEntity;
use MalteHuebner\OrderedEntitiesBundle\TwigExtension\OrderedEntityExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TwigFunction;

class OrderedEntityExtensionTest extends TestCase
{
    public function testRegistersPreviousAndNextEntityFunctions(): void
    {
        $extension = new OrderedEntityExtension($this->createStub(OrderedEntitiesManagerInterface::class));

        $names = array_map(static fn (TwigFunction $f) => $f->getName(), $extension->getFunctions());

        $this->assertSame(['previous_entity', 'next_entity'], $names);
    }

    public function testFunctionsAreMarkedHtmlSafe(): void
    {
        $extension = new OrderedEntityExtension($this->createStub(OrderedEntitiesManagerInterface::class));

        foreach ($extension->getFunctions() as $function) {
            $this->assertSame(['html'], $function->getSafe(new \Twig\Node\Node()), $function->getName());
        }
    }

    public function testPreviousEntityDelegatesToTheManager(): void
    {
        $entity = new SimpleOrderEntity(2);
        $previous = new SimpleOrderEntity(1);
        $manager = $this->createMock(OrderedEntitiesManagerInterface::class);
        $manager->expects($this->once())->method('getPrevious')->with($entity)->willReturn($previous);
        $manager->expects($this->never())->method('getNextEntity');

        $this->assertSame($previous, (new OrderedEntityExtension($manager))->previousEntity($entity));
    }

    public function testNextEntityDelegatesToTheManager(): void
    {
        $entity = new SimpleOrderEntity(2);
        $next = new SimpleOrderEntity(3);
        $manager = $this->createMock(OrderedEntitiesManagerInterface::class);
        $manager->expects($this->once())->method('getNextEntity')->with($entity)->willReturn($next);
        $manager->expects($this->never())->method('getPrevious');

        $this->assertSame($next, (new OrderedEntityExtension($manager))->nextEntity($entity));
    }

    public function testNullNeighboursArePassedThrough(): void
    {
        $manager = $this->createStub(OrderedEntitiesManagerInterface::class);
        $manager->method('getPrevious')->willReturn(null);
        $manager->method('getNextEntity')->willReturn(null);
        $extension = new OrderedEntityExtension($manager);

        $this->assertNull($extension->previousEntity(new SimpleOrderEntity(1)));
        $this->assertNull($extension->nextEntity(new SimpleOrderEntity(1)));
    }

    public function testFunctionsAreUsableFromTemplates(): void
    {
        $manager = $this->createStub(OrderedEntitiesManagerInterface::class);
        $manager->method('getPrevious')->willReturn(new SimpleOrderEntity(1));
        $manager->method('getNextEntity')->willReturn(null);

        $twig = new Environment(new ArrayLoader([
            'nav' => '{% set p = previous_entity(entity) %}{% set n = next_entity(entity) %}'
                . 'prev={{ p ? p.position : "none" }};next={{ n ? n.position : "none" }}',
        ]));
        $twig->addExtension(new OrderedEntityExtension($manager));

        $this->assertSame('prev=1;next=none', $twig->render('nav', ['entity' => new SimpleOrderEntity(2)]));
    }

    public function testGetName(): void
    {
        $extension = new OrderedEntityExtension($this->createStub(OrderedEntitiesManagerInterface::class));

        $this->assertSame('ordered_entity_extension', $extension->getName());
    }
}
