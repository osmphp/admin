<?php

namespace Osm\Admin\Schema\Property;

use Osm\Admin\Schema\Property;
use Osm\Admin\Schema\Record;
use Osm\Admin\Schema\Struct;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Exceptions\Required;
use Osm\Core\Object_;

/**
 * @property string $refs_name
 * @property string $refs_root_class_name
 * @property string $ref_class_name #[Serialized]
 * @property string[]|null $ref_if #[Serialized]
 * @property Struct $ref
 *
 * @uses Serialized
 */
class Bag extends Property
{
    protected function get_refs_name(): string {
        throw new NotImplemented($this);
    }

    protected function get_refs_root_class_name(): string {
        throw new NotImplemented($this);
    }
    protected function get_ref_class_name(): string {
        throw new Required(__METHOD__);
    }

    protected function get_ref_if(): ?array {
        throw new Required(__METHOD__);
    }

    protected function get_ref(): Struct {
        return $this->parent->schema->{$this->refs_name}[$this->ref_class_name];
    }

    public function parse(): void
    {
        global $osm_app; /* @var App $osm_app */

        parent::parse();

        $reflection = $osm_app->classes[$this->reflection->type];

        $this->ref_class_name = $this->reflection->type;
        $this->ref_if = $this->parent->schema->parseTypes(
            $reflection, $this->refs_root_class_name);
    }
}