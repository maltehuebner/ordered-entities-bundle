<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\DependencyInjection;

use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilder;
use MalteHuebner\OrderedEntitiesBundle\DependencyInjection\MalteHuebnerOrderedEntitiesExtension;
use MalteHuebner\OrderedEntitiesBundle\MalteHuebnerOrderedEntitiesBundle;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntitiesManager;
use MalteHuebner\OrderedEntitiesBundle\TwigExtension\OrderedEntityExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class MalteHuebnerOrderedEntitiesExtensionTest extends TestCase
{
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        (new MalteHuebnerOrderedEntitiesExtension())->load([], $this->container);
    }

    public function testExtendsTheDependencyInjectionExtensionBaseClass(): void
    {
        // Symfony 8.1 deprecates the HttpKernel Extension base class.
        $this->assertInstanceOf(Extension::class, new MalteHuebnerOrderedEntitiesExtension());
    }

    public function testAliasIsDerivedFromTheClassName(): void
    {
        $this->assertSame('malte_huebner_ordered_entities', (new MalteHuebnerOrderedEntitiesExtension())->getAlias());
    }

    public function testRegistersThreePublicServices(): void
    {
        foreach ([CriteriaBuilder::class, OrderedEntitiesManager::class, OrderedEntityExtension::class] as $id) {
            $this->assertTrue($this->container->hasDefinition($id), $id);
            $this->assertTrue($this->container->getDefinition($id)->isPublic(), $id.' should be public');
        }
    }

    public function testManagerIsWiredWithDoctrineAndTheCriteriaBuilder(): void
    {
        $arguments = $this->container->getDefinition(OrderedEntitiesManager::class)->getArguments();

        $this->assertCount(2, $arguments);
        $this->assertInstanceOf(Reference::class, $arguments[0]);
        $this->assertSame('doctrine', (string) $arguments[0]);
        $this->assertInstanceOf(Reference::class, $arguments[1]);
        $this->assertSame(CriteriaBuilder::class, (string) $arguments[1]);
    }

    public function testTwigExtensionIsTaggedAndReceivesTheManager(): void
    {
        $definition = $this->container->getDefinition(OrderedEntityExtension::class);

        $this->assertTrue($definition->hasTag('twig.extension'));
        $this->assertSame(OrderedEntitiesManager::class, (string) $definition->getArgument(0));
    }

    public function testContainerCompilesWithADoctrineService(): void
    {
        $this->container->register('doctrine', \Doctrine\Persistence\ManagerRegistry::class)
            ->setSynthetic(true)
            ->setPublic(true);
        $this->container->compile();
        $this->container->set('doctrine', $this->createStub(\Doctrine\Persistence\ManagerRegistry::class));

        $this->assertInstanceOf(OrderedEntitiesManager::class, $this->container->get(OrderedEntitiesManager::class));
        $this->assertInstanceOf(OrderedEntityExtension::class, $this->container->get(OrderedEntityExtension::class));
    }

    public function testBundleExposesTheExtension(): void
    {
        $bundle = new MalteHuebnerOrderedEntitiesBundle();

        $this->assertInstanceOf(MalteHuebnerOrderedEntitiesExtension::class, $bundle->getContainerExtension());
    }
}
