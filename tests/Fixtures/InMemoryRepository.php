<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

/**
 * Repository double that evaluates Criteria in memory, exactly like
 * ArrayCollection::matching() does — so the tests exercise the real
 * expression semantics instead of asserting on mock call arguments.
 *
 * @implements ObjectRepository<object>
 * @implements Selectable<int, object>
 */
class InMemoryRepository implements ObjectRepository, Selectable
{
    /** @var ArrayCollection<int, object> */
    private ArrayCollection $collection;

    /** @var list<Criteria> */
    public array $receivedCriteria = [];

    /** @param list<object> $entities */
    public function __construct(array $entities)
    {
        $this->collection = new ArrayCollection($entities);
    }

    public function matching(Criteria $criteria): ReadableCollection
    {
        $this->receivedCriteria[] = $criteria;

        return $this->collection->matching($criteria);
    }

    public function find(mixed $id): ?object
    {
        return null;
    }

    public function findAll(): array
    {
        return $this->collection->toArray();
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return [];
    }

    public function findOneBy(array $criteria): ?object
    {
        return null;
    }

    public function getClassName(): string
    {
        return \stdClass::class;
    }
}
