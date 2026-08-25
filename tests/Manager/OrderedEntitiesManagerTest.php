<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilder;
use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilderInterface;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntitiesManager;
use MalteHuebner\OrderedEntitiesBundle\SortOrder;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\CategorizedEntity;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\InMemoryRepository;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\SimpleOrderEntity;
use PHPUnit\Framework\TestCase;

class OrderedEntitiesManagerTest extends TestCase
{
    public function testGetPreviousReturnsTheClosestSmallerEntity(): void
    {
        [$manager, $repository] = $this->managerFor(new SimpleOrderEntity(1), new SimpleOrderEntity(4), new SimpleOrderEntity(9), new SimpleOrderEntity(6));

        $previous = $manager->getPrevious(new SimpleOrderEntity(7));

        $this->assertInstanceOf(SimpleOrderEntity::class, $previous);
        $this->assertSame(6, $previous->getPosition());
    }

    public function testGetNextEntityReturnsTheClosestGreaterEntity(): void
    {
        [$manager] = $this->managerFor(new SimpleOrderEntity(1), new SimpleOrderEntity(4), new SimpleOrderEntity(9), new SimpleOrderEntity(6));

        $next = $manager->getNextEntity(new SimpleOrderEntity(5));

        $this->assertInstanceOf(SimpleOrderEntity::class, $next);
        $this->assertSame(6, $next->getPosition());
    }

    public function testGetPreviousReturnsNullForTheFirstEntity(): void
    {
        [$manager] = $this->managerFor(new SimpleOrderEntity(1), new SimpleOrderEntity(2));

        $this->assertNull($manager->getPrevious(new SimpleOrderEntity(1)));
    }

    public function testGetNextEntityReturnsNullForTheLastEntity(): void
    {
        [$manager] = $this->managerFor(new SimpleOrderEntity(1), new SimpleOrderEntity(2));

        $this->assertNull($manager->getNextEntity(new SimpleOrderEntity(2)));
    }

    public function testBothDirectionsReturnNullOnAnEmptyRepository(): void
    {
        [$manager] = $this->managerFor();

        $this->assertNull($manager->getPrevious(new SimpleOrderEntity(5)));
        $this->assertNull($manager->getNextEntity(new SimpleOrderEntity(5)));
    }

    public function testNeighboursRespectIdenticalAndBooleanAttributes(): void
    {
        $sameCategoryEnabled = new CategorizedEntity(3, 'news', enabled: true, name: 'expected');
        [$manager] = $this->managerFor(
            new CategorizedEntity(4, 'news', enabled: false, name: 'disabled sibling'),
            new CategorizedEntity(4, 'blog', enabled: true, name: 'other category'),
            $sameCategoryEnabled,
            new CategorizedEntity(1, 'news', enabled: true, name: 'further away'),
        );

        $previous = $manager->getPrevious(new CategorizedEntity(5, 'news'));

        $this->assertSame($sameCategoryEnabled, $previous);
    }

    public function testRepositoryIsLookedUpByTheConcreteEntityClass(): void
    {
        $repository = new InMemoryRepository([]);
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->exactly(2))
            ->method('getRepository')
            ->with(SimpleOrderEntity::class)
            ->willReturn($repository);

        $manager = new OrderedEntitiesManager($registry, new CriteriaBuilder());
        $manager->getPrevious(new SimpleOrderEntity(1));
        $manager->getNextEntity(new SimpleOrderEntity(1));
    }

    public function testCriteriaBuilderReceivesTheDirectionMatchingTheCall(): void
    {
        $entity = new SimpleOrderEntity(1);
        $builder = $this->createMock(CriteriaBuilderInterface::class);
        $builder->expects($this->exactly(2))
            ->method('build')
            ->willReturnCallback(function (SimpleOrderEntity $given, string $direction) use ($entity, &$directions): Criteria {
                $this->assertSame($entity, $given);
                $directions[] = $direction;

                return Criteria::create();
            });
        $directions = [];

        $repository = new InMemoryRepository([]);
        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getRepository')->willReturn($repository);

        $manager = new OrderedEntitiesManager($registry, $builder);
        $manager->getPrevious($entity);
        $manager->getNextEntity($entity);

        $this->assertSame([SortOrder::ASC, SortOrder::DESC], $directions);
        $this->assertCount(2, $repository->receivedCriteria);
    }

    public function testTheLastElementOfTheMatchedCollectionIsReturned(): void
    {
        // Whatever the repository returns, the manager takes the last element —
        // this is why the criteria order candidates so the closest one is last.
        $first = new SimpleOrderEntity(1);
        $last = new SimpleOrderEntity(2);
        $repository = $this->createStub(\MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\InMemoryRepository::class);
        $repository->method('matching')->willReturn(new ArrayCollection([$first, $last]));
        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getRepository')->willReturn($repository);

        $manager = new OrderedEntitiesManager($registry, new CriteriaBuilder());

        $this->assertSame($last, $manager->getPrevious(new SimpleOrderEntity(9)));
    }

    /**
     * @return array{0: OrderedEntitiesManager, 1: InMemoryRepository}
     */
    private function managerFor(object ...$entities): array
    {
        $repository = new InMemoryRepository(array_values($entities));
        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getRepository')->willReturn($repository);

        return [new OrderedEntitiesManager($registry, new CriteriaBuilder()), $repository];
    }
}
