<?php

namespace Osm\Admin\Ui;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Queries\Query as DbQuery;
use Osm\Admin\Schema\Table;
use Osm\Admin\Ui\Exceptions\InvalidQuery;
use Osm\Admin\Ui\Hints\UrlAction;
use Osm\Admin\Ui\Query\Filter as QueryFilter;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;
use Osm\Framework\Search\Query as SearchQuery;
use function Osm\__;
use function Osm\query;
use function Osm\url_encode;

/**
 * @property Table $table
 * @property DbQuery $db_query
 * @property Result $result
 */
class Query extends Object_
{
    public bool $count = false;
    public bool $items = true;
    public bool $all = false;
    /**
     * @var Formula\SelectExpr[]
     */
    public array $selects = [];
    /**
     * @var string[]
     */
    public array $facets = [];
    /**
     * @var Filter[]
     */
    public array $filters = [];
    public array $url = [];

    public static array $url_operators = ['-'];

    public function whereIn(string $propertyName, array $items): static
    {
        $this->filters[$propertyName] = QueryFilter\In_::new([
            'query' => $this,
            'property_name' => $propertyName,
            'items' => $items,
        ]);

        return $this;
    }

    public function whereNotIn(string $propertyName, array $items): static
    {
        $this->filters[] = QueryFilter\In_::new([
            'query' => $this,
            'property_name' => $propertyName,
            'items' => $items,
            'not' => true,
        ]);

        return $this;
    }

    protected function get_table(): Table {
        throw new Required(__METHOD__);
    }

    protected function dbQuery(): DbQuery {
        return query($this->table->name);
    }

    protected function searchQuery(): SearchQuery {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->search->index($this->table->table_name);
    }

    protected function get_db_query(): DbQuery {
        return $this->dbQuery();
    }

    public function select(string|array ...$formulas): static {
        $this->db_query->select(...$formulas);
        $this->selects = $this->db_query->selects;

        return $this;
    }

    /**
     * Request total record count. After retrieving query result it's written
     * into the `$count` property.
     *
     * @param bool $count
     * @return $this
     */
    public function count(bool $count = true): static {
        $this->count = $count;

        return $this;
    }

    public function items(bool $items = true): static {
        $this->items = $items;

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
        $this->all = $all;

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
    public function fromUrl(array $query, string ...$ignored): static {
        foreach ($query as $key => $value) {
            if (in_array($key, $ignored)) {
                continue;
            }

            switch ($key) {
                case 'all':
                case 'limit':
                case 'offset':
                case 'order':
                case 'q':
                case 'select':
                    throw new NotImplemented($this);
                default:
                    $this->parseUrlFilter($key, $value);
                    break;
            }
        }

        return $this;
    }

    protected function parseUrlFilter(string $key, string|bool|array $value)
        : void
    {
        $operator = '';
        foreach (static::$url_operators as $urlOperator) {
            if (str_ends_with($key, $urlOperator)) {
                $operator = $urlOperator;
                $key = mb_substr($key, 0, mb_strlen($key) -
                    mb_strlen($urlOperator));
                break;
            }
        }

        if (!($property = $this->table->properties[$key] ?? null)) {
            return;
        }

        if (!$property->index_filterable) {
            return;
        }

        $property->parseUrlFilter($this, $operator, $value);
    }

    /**
     * Generates URL based on the current query parameters, and requested
     * `$actions`.
     *
     * @param string $routeName
     * @param UrlAction[] $actions
     * @return string
     */
    public function toUrl(string $routeName, array $actions)
        : string
    {
        $url = $this->table->url($routeName);

        $parameters = $this->url;

        foreach ($actions as $action) {
            switch ($action->type) {
                case UrlAction::REMOVE_ALL_FILTERS:
                    foreach (array_keys($parameters) as $param) {
                        if (isset($this->table->properties[$param])) {
                            unset($parameters['param']);
                        }
                    }
                    break;
                case UrlAction::REMOVE_PARAMETER:
                    unset($parameters[$action->param]);
                    break;
                case UrlAction::REMOVE_OPTION:
                    if (isset($parameters[$action->param]) &&
                        ($index = array_search($action->value,
                            $parameters[$action->param])) !== false)
                    {
                        array_splice($parameters[$action->param],
                            $index, 1);

                        if (empty($parameters[$action->param])) {
                            unset($parameters[$action->param]);
                        }
                    }
                    break;
                case UrlAction::SET_PARAMETER:
                    $parameters[$action->param] = $action->value !== null
                        ? [$action->value]
                        : null;
                    break;
                case UrlAction::ADD_OPTION:
                    if (!isset($parameters[$action->param])) {
                        $parameters[$action->param] = [];
                    }
                    $parameters[$action->param][] = $action->value;
                    break;
            }
        }

        $parameterUrl = '';
        foreach ($parameters as $param => $values) {
            if ($parameterUrl) {
                $parameterUrl .= '&';
            }

            $parameterUrl .= $param . ($values !== null
                ? '=' . implode('+', array_map(
                    fn($value) => url_encode($value),
                    $values
                ))
                : '');
        }

        return $parameterUrl ? "{$url}?{$parameterUrl}" : $url;
    }
    
    /**
     * Request facet counts or stats. Triggers using the search index.
     *
     * @param string $propertyName
     * @return $this
     */
    public function facet(string $propertyName): static
    {
        $this->facets[] = $propertyName;

        return $this;
    }

    protected function get_result(): Result
    {
        return !empty($this->facets)
            ? $this->runSearchAndDb()
            : $this->runDb();
    }

    protected function runSearchAndDb(): Result {
        $searchQuery = $this->searchQuery();

        $facets = array_unique($this->facets);
        foreach ($facets as $propertyName) {
            if (isset($this->filters[$propertyName])) {
                continue;
            }

            $this->table->properties[$propertyName]->facet->query($searchQuery);
        }

        foreach ($this->filters as $filter) {
            $filter->querySearch($searchQuery);
        }

        $searchResult = $searchQuery
            ->count()
            ->limit(10000)
            ->get();

        $result = Result::new();
        $result->count = $searchResult->count;


        $result->facets = [];
        foreach ($facets as $propertyName) {
            if (isset($this->filters[$propertyName])) {
                continue;
            }

            $result->facets[$propertyName] =
                $this->table->properties[$propertyName]->facet
                    ->populate($this, $searchResult);
        }


        $result->items = count($searchResult->ids)
            ? $this->db_query
                ->where("id IN (" .
                    implode(', ', $searchResult->ids). ")")
                ->get()
            : [];

        foreach ($facets as $propertyName) {
            if (!isset($this->filters[$propertyName])) {
                continue;
            }

            $searchQuery = $this->searchQuery()->hits(false);
            $this->table->properties[$propertyName]->facet->query($searchQuery);

            foreach ($this->filters as $filteredPropertyName => $filter) {
                if ($filteredPropertyName !== $propertyName) {
                    $filter->querySearch($searchQuery);
                }
            }

            $searchResult = $searchQuery->get();

            $result->facets[$propertyName] =
                $this->table->properties[$propertyName]->facet
                    ->populate($this, $searchResult);
        }

        return $result;
    }

    protected function runDb(): Result {
        $result = Result::new();

        if ($this->count) {
            $result->count = $this->dbQuery()->value("COUNT() AS count");
        }

        foreach ($this->filters as $filter) {
            $filter->queryDb($this->db_query);
        }

        if ($this->items) {
            $result->items = $this->db_query->get();
        }

        return $result;
    }

    protected function get_query_url(): array {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->http->query;
    }
}