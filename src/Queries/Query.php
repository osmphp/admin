<?php

namespace Osm\Admin\Queries;

use Osm\Admin\Base\Exceptions\SyntaxError;
use Osm\Admin\Base\Exceptions\UndefinedProperty;
use Osm\Admin\Formulas\Formula;
use Osm\Admin\Indexing\Index;
use Osm\Admin\Queries\Traits\Where;
use Osm\Admin\Storages\Storage;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Admin\Schema\Class_;
use function Osm\__;
use function Osm\formula;

/**
 * @property Storage $storage
 * @property Class_ $class
 * @property ?int $limit
 * @property ?Index $index
 * @property bool $hydrate
 */
class Query extends Object_
{
    use Where;

    /**
     * @var Formula[]
     */
    public array $filters = [];

    /**
     * @var Formula[]
     */
    public array $selects = [];

    /**
     * @var Formula\Order[]
     */
    public array $orders = [];

    public const DEFAULT_CHUNK_SIZE = 100;
    public const DEFAULT_LIMIT = 10;

    protected function get_storage(): Storage {
        throw new Required(__METHOD__);
    }

    protected function get_class(): Class_ {
        return $this->storage->class;
    }

    protected function get_limit(): ?int {
        return static::DEFAULT_LIMIT;
    }

    protected function get_index(): ?Index {
        return $this->storage->targeted_by[$this->data->type ?? ''] ?? null;
    }

    public function get(...$formulas): array {
        throw new NotImplemented($this);
    }

    public function chunk(callable $callback,
        int $size = self::DEFAULT_CHUNK_SIZE): void
    {
        throw new NotImplemented($this);
    }

    public function first(...$formulas): \stdClass|Object_|null {
        throw new NotImplemented($this);
    }

    public function value(string $formula): mixed {
        if (($value = $this->first($formula)) === null) {
            return null;
        }

        $parsed = $this->selects[$formula];

        if (!($parsed instanceof Formula\Identifier)) {
            throw new NotImplemented($this);
        }

        foreach (array_keys($parsed->accessors)
            as $propertyName)
        {
            if (($value = $value->$propertyName) === null) {
                return null;
            }
        }

        return $value->{$parsed->property->name};
    }

    public function insert(\stdClass|array $data): int {
        throw new NotImplemented($this);
    }

    public function update(\stdClass|array $data): void {
        throw new NotImplemented($this);
    }

    public function hydrate(): static {
        $this->hydrate = true;
        return $this;
    }

    public function select(...$formulas): static {
        foreach ($formulas as $formula) {
            $parsed = formula($formula, $this->class);

            if ($parsed instanceof Formula\Identifier && $parsed->wildcard) {
                $this->selectWildcard($parsed);
            }
            else {
                $this->selects[$formula] = $parsed;
            }

        }

        return $this;
    }

    protected function selectWildcard(Formula\Identifier $formula): void
    {
        $class = $this->class;
        if (!empty($formula->accessors)) {
            $type = $formula->accessors[count($formula->accessors) - 1]
                ->reflection->type;

            if (!($class = $this->class->schema->classes[$type] ?? null)) {
                throw new SyntaxError(__("Can't resolve ':formula' formula to properties of a data class.", [
                    'formula' => $formula,
                ]));
            }
        }

        $prefix = mb_substr($formula->text, 0, mb_strlen($formula->text) - 1);
        foreach ($class->properties as $property) {
            if (!$property->stored) {
                continue;
            }

            $this->selects["{$prefix}{$property->name}"] =
                Formula\Identifier::new([
                    'text' => "{$prefix}{$property->name}",
                    'accessors' => $formula->accessors,
                    'property' => $property,
                    'wildcard' => false,
                ]);
        }
    }

    public function orderBy(string $formula, bool $desc = false): static {
        $this->orders[$formula] = $parent = Formula\Order::new([
            'expr' => $expr = formula($formula, $this->class),
            'desc' => $desc,
        ]);
        $expr->parent = $parent;

        return $this;
    }
}