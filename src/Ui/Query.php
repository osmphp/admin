<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Queries\Query as DbQuery;
use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Exceptions\InvalidQuery;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Search\Query as SearchQuery;
use function Osm\__;
use function Osm\query;

/**
 * @property Table $table
 * @property DbQuery $db_query
 * @property SearchQuery $search_query
 * @property array $query_url
 * @property int $count
 * @property \stdClass[] $items
 */
class Query extends Object_
{
    protected bool $executed = false;
    protected bool $query_count = false;
    protected bool $query_items = true;
    protected bool $query_all = false;
    /**
     * @var string[]
     */
    protected array $query_facets = [];
    /**
     * @var Filter[]
     */
    protected array $query_filters = [];

    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function get_db_query(): DbQuery {
        return query($this->table->name);
    }

    /**
     * Request total record count. After retrieving query result it's written
     * into the `$count` property.
     *
     * @param bool $count
     * @return $this
     */
    public function count(bool $count = true): static {
        $this->query_count = $count;

        return $this;
    }

    public function items(bool $items = true): static {
        $this->query_items = $items;

        return $this;
    }

    /**
     * Allow a query on all objects without specifying any filters in the URL.
     * Querying all objects is typically allowed on list pages. On other pages,
     * for example, on the form page, you can still operate on all objects
     * using `?all` query parameter.
     *
     * @param bool $all
     * @return $this
     */
    public function all(bool $all = true): static {
        $this->query_all = $all;

        return $this;
    }

    /**
     * Pass URL query parameters shallow-parsed format for further
     * URL parsing and generation. If you don't, `$osm_app->http->query` is used.
     *
     * @param array $query
     * @return $this
     */
    public function url(array $query): static {
        $this->query_url = $query;

        return $this;
    }

    /**
     * Parse HTTP query parameters except `$ignored` ones. It includes:
     * * filter parameters starting with the filtered property name, for
     *      example, `color=red blue&price=5-100&id-=1 2 3`.
     * * `all` - operate without any filters. If there is any filter parameter,
     *      `all` parameter is ignored
     * * `limit` - limit returned object count
     * * `offset` - returns objects starting from Nth
     * * `order` - one or more orders, ` ` (encoded as `+`) meaning ascending order,
     *      and `-` meaning descending order, for example, `order=color-price`.
     *      Either way, `-id` order is implicitly added.
     * * `q` - search specified phrase
     * * `select` - requested select formulas delimited by ` ` (encoded as `+`).
     *      Non-identifier formulas should go in parentheses with mandatory
     *      alias, for example, `select=title color (qty > 0 AS in_stock)`.
     *
     * @param array $query URL query parameters in shallow-parsed format, usually
     *      taken from `$osm_app->http->query`.
     * @param string ...$ignored Ignored parameters, for example,
     *      `'all', 'select', 'id', 'id-'`.
     * @return $this
     */
    public function fromUrl(string ...$ignored): static {
        //throw new NotImplemented($this);
        return $this;
    }

    /**
     * Generates URL based on the current query parameters, and requested
     * `$changes`. Start each of `$changes` with ` ` (encoded as `+`)
     * to add a parameter, or add a value to the existing parameter, or with `-`
     * to remove a parameter value or the whole parameter. For example,
     * ` id=5` or `-offset`. Use `-` to remove all filters.
     *
     * @param string $route
     * @param array $url
     * @param string ...$changes
     * @return string
     */
    public function toUrl(string $route, string ...$changes)
        : string
    {
        //throw new NotImplemented($this);
        return '#';
    }
    
    /**
     * Request facet counts or stats. Triggers using the search index.
     *
     * @param string $propertyName
     * @return $this
     */
    public function facet(string $propertyName): static
    {
        $this->query_facets[$propertyName] = true;

        return $this;
    }

    protected function get_count(): int {
        $this->run();

        if (!isset($this->count)) {
            throw new InvalidQuery(__(
                "To retrieve record count, use `count()` method before accessing query results"));
        }

        return $this->count;
    }

    protected function get_items(): array {
        $this->run();

        if (!isset($this->items)) {
            throw new InvalidQuery(__(
                "To retrieve record data, use `items()` method before accessing query results"));
        }

        return $this->items;
    }

    protected function run(): void
    {
        if ($this->executed) {
            return;
        }

        if (!empty($this->query_facets)) {
            $this->runSearchAndDb();
        }
        else {
            $this->runDb();
        }

        $this->executed = true;
    }

    protected function runSearchAndDb(): void {
        throw new NotImplemented($this);
    }

    protected function runDb(): void {
        if ($this->query_count) {
            $query = query($this->table->name, [
                'filters' => $this->db_query->filters,
            ]);
            $this->count = $query->value("COUNT() AS count");
        }

        if ($this->query_items) {
            $this->items = $this->db_query->get();
        }
    }

    protected function get_query_url(): array {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->http->query;
    }
}