<?php

namespace Osm\Admin\Tables\Traits;

use Osm\Core\Exceptions\NotImplemented;
use function Osm\merge;

trait Update
{
    public function update(\stdClass|array $data): void {
        $this->db->transaction(function() use ($data) {
            $this->db->committed(function () use ($data) {
                $this->updateCommitted($data);
            });

            if (is_array($data)) {
                $data = (object)$data;            
            }

            if ($this->batchUpdating($data)) {
                $this->raw->update($this->updateValues($data));            
                $this->batchUpdated($data);
                return;            
            }

            $this->chunk(function(\stdClass $item) use ($data) {
                $item = merge($item, $data);
                $this->updating($item);
                $this->db->table($this->name)
                    ->where('id', $item->id)
                    ->update($this->updateValues($item));
                $this->updated($item);
            });
        });
    }

    protected function batchUpdating(\stdClass $data): bool
    {
        return false;
        //throw new NotImplemented($this);
    }

    protected function batchUpdated(\stdClass $data): void
    {
        throw new NotImplemented($this);
    }

    protected function updating(\stdClass $data): void
    {
        $this->index?->updating($this, $data);
    }

    protected function updated(\stdClass $data): void
    {
    }

    protected function updateCommitted(\stdClass $data): void
    {
    }

    protected function updateValues(\stdClass $data): array
    {
        $data = (array)$data;
        unset($data['id']);

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
}