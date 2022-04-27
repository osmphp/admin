<?php

namespace Osm\Admin\Schema;

use Osm\Admin\Queries\Query;
use Osm\Admin\Schema\Attributes\Fixture;
use Osm\Admin\Schema\Exceptions\InvalidFixture;
use Osm\Admin\Schema\Exceptions\InvalidRename;
use Osm\Admin\Schema\Hints\Indexer\Status;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Attributes\Type;
use Osm\Core\Class_ as CoreClass;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use function Osm\__;
use function Osm\dehydrate;
use function Osm\hydrate;
use function Osm\sort_by_dependency;

/**
 * @property ?string $fixture_class_name #[Serialized]
 * @property ?int $fixture_version #[Serialized]
 * @property ?string $fixture_namespace #[Serialized]
 * @property ?string $fixture_version_namespace #[Serialized]
 * @property Class_[] $classes #[Serialized]
 * @property Table[] $tables #[Serialized]
 * @property Option[] $option_handlers
 * @property array $options #[Serialized]
 * @property string[] $singleton_class_names #[Serialized]
 * @property Indexer[] $indexers #[Serialized]
 * @property NotificationTable[] $notification_tables #[Serialized]
 * @property Table[] $singletons
 * @property Db $db
 * @property Descendants $descendants
 * @property array $listener_names #[Serialized]
 *
 * @uses Serialized
 */
class Schema extends Object_
{
    protected function get_classes(): array {
        throw new Required(__METHOD__);
    }

    protected function get_tables(): array {
        throw new Required(__METHOD__);
    }

    public function __wakeup(): void {
        foreach ($this->classes as $class) {
            $class->schema = $this;
        }

        foreach ($this->tables as $table) {
            $table->schema = $this;
        }

        foreach ($this->indexers as $indexer) {
            $indexer->schema = $this;
        }

        foreach ($this->notification_tables as $notificationTable) {
            $notificationTable->schema = $this;
        }
    }

    public function migrate(\stdClass|Schema $old = null,
        OutputInterface $output = null, bool $dryRun = false): void
    {
        if (!$old &&
            ($json = $this->db->table('schema')->value('current')))
        {
            $old = json_decode($json);
        }

        $schema = Diff\Schema::new([
            'old' => $old,
            'new' => $this,
            'output' => $output ?? new BufferedOutput(),
            'dry_run' => $dryRun,
        ]);

        $schema->diff();

        $schema->migrate();

        $this->db->table('schema')->update([
            'current' => json_encode(dehydrate($this)),
        ]);
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_descendants(): Descendants {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->descendants;
    }

    public function parse(): static {
        global $osm_app; /* @var App $osm_app */

        $this->classes = [];
        $this->tables = [];

        $recordClass = $osm_app->classes[Record::class];

        foreach ($recordClass->child_class_names as $baseClassName) {
            if ($this->belongs($baseClassName)) {
                $this->parseTable($osm_app->classes[$baseClassName]);
            }
        }

        if ($this->fixture_version_namespace) {
            // in a schema migration test, fail if there is not a single
            // data class defined for a given fixture version
            if (empty($this->tables)) {
                throw new InvalidFixture(__(
                    "No data classes defined in the ':namespace' schema fixture version namespace",
                    ['namespace' => $this->fixture_version_namespace]));
            }
        }

        foreach ($this->tables as $table) {
            $table->parse();
        }

        foreach ($this->classes as $class) {
            $class->parse();
        }

        $this->tables = sort_by_dependency($this->tables, 'Tables',
            fn($positions) =>
                fn(Table $a, Table $b) =>
                    $positions[$a->name] <=> $positions[$b->name]
        );

        return $this;
    }

    protected function parseTable(CoreClass $reflection): void {
        $name = $this->getUnversionedName($reflection->name);

        if (isset($this->tables[$name])) {
            return;
        }

        $this->tables[$name] = $table = Table::new([
            'schema' => $this,
            'name' => $name,
            'reflection' => $reflection,
        ]);

        $this->parseProperties($table);
    }

    protected function parseClass(CoreClass $reflection): void {
        if (isset($this->classes[$reflection->name])) {
            return;
        }

        $name = $this->getUnversionedName($reflection->name);
        $this->classes[$name] = $class = Class_::new([
            'schema' => $this,
            'name' => $name,
            'reflection' => $reflection,
        ]);

        $this->parseProperties($class);
    }

    protected function parseProperties(Struct $struct): void {
        foreach ($struct->reflection->properties as $property) {
            if ($property->name != '__class') {
                $this->parseType($property->type);
            }
        }
    }

    protected function parseType(?string $type): void {
        global $osm_app; /* @var App $osm_app */

        if (!$type) {
            return;
        }

        if (!($class = $osm_app->classes[$type] ?? null)) {
            return;
        }

        for(; $class; $class = $class->parent_class) {
            if ($class->parent_class_name == Table::ROOT_CLASS_NAME) {
                $this->parseTable($class);
                break;
            }

            if ($class->parent_class_name == Class_::ROOT_CLASS_NAME) {
                $this->parseClass($class);
                break;
            }
        }
    }

    public function parseTypes(CoreClass $reflection,
        string $rootClassName): ?array
    {
        global $osm_app; /* @var App $osm_app */

        if ($reflection->parent_class_name === $rootClassName) {
            return null;
        }

        $types = [];

        /* @var Type $type */
        if ($type = $reflection->attributes[Type::class] ?? null) {
            $types[] = $type->name;
        }

        foreach ($reflection->child_class_names as $childClassName) {
            $types = array_merge($types, $this->parseTypes(
                $osm_app->classes[$childClassName], $rootClassName));
        }

        sort($types);
        return array_unique($types);
    }

    protected function get_singleton_class_names(): array {
        $tableNames = [];

        foreach ($this->tables as $table) {
            if ($table->singleton) {
                $tableNames[$table->table_name] = $table->name;
            }
        }

        return $tableNames;
    }

    protected function get_singletons(): array {
        return array_map(fn(string $className) => $this->tables[$className],
            $this->singleton_class_names);
    }

    protected function get_option_handlers(): array {
        $optionHandlers = [];

        $classes = $this->descendants->all(Option::class);

        foreach ($classes as $className) {
            $new = "{$className}::new";

            /* @var Option $instance */
            $optionHandlers[$className] = $new();
        }

        return $optionHandlers;
    }

    protected function get_options(): array {
        return array_map(fn(Option $handler) => $handler->get(),
            $this->option_handlers);
    }

    /**
     * In `PARTIAL` mode, processes all pending change notifications
     * for all tables. In `FULL` mode, calculates all indexes on all
     * tables anew.
     *
     * @param string $mode `Indexer::PARTIAL` or `Indexer::FULL`
     */
    public function index(string $mode = Indexer::PARTIAL): void {
        $status = $this->getIndexerStatus();

        foreach ($this->indexers as $indexer) {
            if ($indexerMode = $indexer->requiresReindex($status, $mode)) {
                $this->db->transaction(function()
                    use($indexer, &$status, $indexerMode)
                {
                    $indexer->index($indexerMode);
                    $indexer->markAsIndexed($status);
                });

            }
        }
    }

    /**
     * Queue processing of all pending change notifications on all tables,
     * or process them right away if queue is not configured
     */
    public function indexAsync(): void {
        throw new NotImplemented($this);
    }

    protected function get_indexers(): array {
        $this->indexers = [];

        foreach ($this->tables as $table) {
            foreach ($table->indexers as $name => $indexer) {
                $indexer->schema = $this;
                $indexer->table_name = $table->name;
                $indexer->short_name = $name;
                $this->indexers[$indexer->name] = $indexer;
            }
        }

        return sort_by_dependency($this->indexers, 'Indexers',
            fn($positions) =>
                fn(Indexer $a, Indexer $b) =>
                    $positions[$a->name] <=> $positions[$b->name]
        );
    }

    /**
     * @return Status[]
     */
    protected function getIndexerStatus(): array {
        return $this->db->table('indexers')
            ->get(['id', 'requires_partial_reindex', 'requires_full_reindex'])
            ->keyBy('id')
            ->toArray();
    }

    protected function get_listener_names(): array {
        $listenerNames = array_map(fn(Table $table) => [], $this->tables);

        foreach ($this->indexers as $indexer) {
            foreach (array_keys($indexer->listens_to) as $tableName) {
                $listenerNames[$tableName][] = $indexer->name;
            }
        }

        return $listenerNames;
    }

    public function isNameVersioned(string $className): bool {
        return (bool)preg_match('/\\\\V\d{3}\\\\/', $className);
    }

    protected function get_fixture_namespace(): ?string {
        if (!$this->fixture_class_name) {
            return null;
        }

        if (!preg_match('/\\\\V\d{3}\\\\/',
            $this->fixture_class_name, $match,
            PREG_OFFSET_CAPTURE))
        {
            throw new InvalidFixture(__(
                "Schema fixture class name ':class' doesn't have version namespace, for example, 'V001'",
                ['class' => $this->fixture_class_name]));
        }

        return substr($this->fixture_class_name, 0,
            $match[0][1] + 1);
    }

    protected function get_fixture_version_namespace(): ?string {
        return $this->fixture_namespace
            ? $this->fixture_namespace . 'V' .
                sprintf('%03d', $this->fixture_version) . '\\'
            : null;
    }

    protected function belongs(mixed $className): bool {
        global $osm_app; /* @var App $osm_app */

        $class = $osm_app->classes[$className];

        if ($this->fixture_version_namespace) {
            // in a schema migration test, load all record classes marked with
            // the `#[Fixture]` attribute and under the
            // fixture version namespace, for example,
            // `\Osm\Admin\Samples\Migrations\String_\V001\`
            return isset($class->attributes[Fixture::class]) &&
                str_starts_with($className, $this->fixture_version_namespace);
        }
        else {
            // in production, load all record classes except having the
            // `#[Fixture]` attribute
            return !isset($class->attributes[Fixture::class]);
        }
    }

    public function getUnversionedName(string $className): string {
        if (!$this->fixture_class_name) {
            return $className;
        }

        if (!$this->isNameVersioned($className)) {
            return $className;
        }

        return $this->fixture_namespace . substr($className,
            strlen($this->fixture_version_namespace));
    }

    public function getVersionedName(string $className): string {
        if (!$this->fixture_class_name) {
            return $className;
        }

        return $this->fixture_version_namespace . substr($className,
                strlen($this->fixture_namespace));
    }

    protected function get_notification_tables(): array {
        $tables = [];

        foreach ($this->tables as $table) {
            foreach ($table->listeners as $indexer) {
                $listensTo = $indexer->listens_to[$table->name];

                if ($inserted = $listensTo[Query::INSERTED] ?? null) {
                    $this->registerNotificationTable($tables, $table,
                        $indexer, $inserted, cascade: true);
                }

                if (($updated = $listensTo[Query::UPDATED] ?? null) &&
                    $updated != $inserted)
                {
                    $this->registerNotificationTable($tables, $table,
                        $indexer, $updated, cascade: true);
                }

                if ($deleted = $listensTo[Query::DELETED] ?? null) {
                    $this->registerNotificationTable($tables, $table,
                        $indexer, $deleted, cascade: false);
                }
            }
        }

        return $tables;
    }

    protected function registerNotificationTable(array &$tables, Table $table,
        Indexer $indexer, mixed $suffix, bool $cascade): void
    {
        $notificationTable = NotificationTable::new([
            'schema' => $this,
            'table_name' => $table->name,
            'table' => $table,
            'indexer_id' => $indexer->id,
            'indexer' => $indexer,
            'suffix' => $suffix,
            'cascade' => $cascade,
        ]);

        $tables[$notificationTable->name] = $notificationTable;
    }
}