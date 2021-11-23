<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Admin\Schema\Class_;
use Osm\Admin\Tables\Table;
use Osm\Framework\Db\Db;
use function Osm\merge;

/**
 * @property Class_ $class (prepared in class)
 * @property Table $storage (prepared in class)
 * @property Db $db (prepared in class)
 * @property string $name (prepared in class)
 *
 * @property \stdClass $data
 * @property string[] $data_after_insert
 * @property array $insert_values
 * @property array $update_after_insert_values
 */
trait Insert
{
    public function insert(\stdClass|array $data): int {
        $this->data = is_array($data) ? (object)$data : clone $data;
        $this->inserting();

        return $this->db->transaction(function() {
            $this->data->id = $this->raw->insertGetId($this->insert_values);

            $this->data_after_insert = [];
            $this->inserted();
            if (!empty($this->data_after_insert)) {
                $this->db->table($this->name)
                    ->where('id', $this->data->id)
                    ->update($this->update_after_insert_values);
            }

            $this->db->committed(function () {
                $this->insertCommitted();
            });

            return $this->data->id;
        });
    }

    protected function inserting(): void {
        $this->index?->inserting($this);
    }

    protected function inserted(): void {
        $this->index?->inserted($this);
    }

    protected function insertCommitted(): void {
    }

    protected function get_insert_values(): array {
        $data = (array)$this->data;
        $values = [];

        foreach ($data as $propertyName => $value) {
            if (!isset($this->class->properties[$propertyName])) {
                unset($data[$propertyName]);
            }
        }

        foreach ($this->storage->columns as $column) {
            if (isset($data[$column->name])) {
                $values[$column->name] = $data[$column->name];
                unset($data[$column->name]);
            }
        }

        $values['data'] = !empty($data) ? json_encode((object)$data) : null;

        return $values;
    }

    protected function get_update_after_insert_values(): array {
        $data = (array)$this->data;
        $values = [];

        foreach ($data as $propertyName => $value) {
            if (!isset($this->data_after_insert[$propertyName])) {
                unset($data[$propertyName]);
                continue;
            }

            if (!isset($this->class->properties[$propertyName])) {
                unset($data[$propertyName]);
            }
        }

        foreach ($this->storage->columns as $column) {
            if (isset($data[$column->name])) {
                $values[$column->name] = $data[$column->name];
                unset($data[$column->name]);
            }
        }

        if (!empty($data)) {
            $data = (object)$data;

            if (!empty($this->insert_values['data'])) {
                $data = merge(json_decode($this->insert_values['data']), $data);
            }

            $values['data'] = json_encode($data);
        }

        return $values;
    }
}