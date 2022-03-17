<?php

namespace Osm\Admin\Queries\Formula;

use Osm\Admin\Queries\Formula;
use Osm\Admin\Queries\Parser;
use Osm\Admin\Schema\DataType;
use Osm\Admin\Schema\Module;
use Osm\Admin\Schema\Table;
use Osm\Core\App;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Exceptions\Required;
use function Osm\__;

/**
 * @property mixed $parameter
 * @property int $index #[Serialized]
 *
 * @uses Serialized
 */
class Parameter extends Formula
{
    public $type = self::PARAMETER;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    protected function get_parameter(): mixed {
        throw new Required(__METHOD__);
    }

    protected function get_index(): int {
        throw new Required(__METHOD__);
    }

    public function resolve(Table $table): void
    {
        global $osm_app; /* @var App $osm_app */

        $dataTypes = $osm_app->modules[Module::class]->data_types;

        if (is_bool($this->parameter)) {
            $this->data_type = $dataTypes['bool'];
        }
        elseif (is_int($this->parameter)) {
            $this->data_type = $dataTypes['int'];
        }
        elseif (is_float($this->parameter)) {
            $this->data_type = $dataTypes['float'];
        }
        elseif (is_string($this->parameter)) {
            $this->data_type = $dataTypes['string'];
        }
        else {
            throw new NotSupported(__(
                "Type of ':parameter' is not supported",
                ['parameter' => $this->parameter]));
        }
        $this->array = false;
    }

    public function toSql(array &$bindings, array &$from, string $join): string
    {
        $bindings[] = $this->parameter;

        return '?';
    }
}