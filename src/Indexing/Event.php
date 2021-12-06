<?php

namespace Osm\Admin\Indexing;

use Illuminate\Database\Schema\Blueprint;
use Osm\Admin\Base\Exceptions\UndefinedMethod;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Core\Traits\SubTypes;
use Osm\Framework\Db\Db;
use function Osm\__;

/**
 * @property Indexer $indexer
 * @property ?int $id #[Serialized]
 * @property string $name #[Serialized]
 * @property string $table #[Serialized]
 * @property bool $notify_inserted
 * @property bool $notify_updated
 * @property bool $notify_deleting
 * @property string[] $depends_on #[Serialized]
 * @property string[] $notified_with #[Serialized]
 * @property string $notification_table
 * @property Db $db
 */
class Event extends Object_
{
    use SubTypes;

    public function create(): void {
        throw new NotImplemented($this);
    }

    public function trigger(\stdClass $data): void
    {
        $arguments = [];
        foreach ($this->notified_with as $property) {
            $arguments[] = $data->{$property} ?? null;
        }

        $this->handle(...$arguments);
    }

    public function changed(): bool
    {
        return (bool)$this->db->table('events')
            ->where('id', $this->id)
            ->value('changed');
    }

    public function clearChangedFlag(): void
    {
        $this->clear();

        $this->db->table('events')
            ->where('id', $this->id)
            ->update(['changed' => false]);
    }

    protected function get_depends_on(): array {
        $dependsOn = [];

        foreach ($this->indexer->depends_on as $formula) {
            if ($this->name != 'this') {
                if (!str_starts_with($formula, "{$this->name}.")) {
                    continue;
                }

                $formula = mb_substr($formula, mb_strlen("{$this->name}."));
            }

            if (mb_strpos($formula, '.') !== false) {
                continue;
            }

            $dependsOn[] = $formula;
        }

        return $dependsOn;
    }

    protected function get_notified_with(): array {
        if (!($method = $this->__class->methods['handle'] ?? null)) {
            throw new UndefinedMethod(__("Implement ':class.:method' method", [
                'class' => $this->__class->name,
                'method' => 'handle',
            ]));
        }

        $notifiedWith = [];

        foreach ($method->reflection->getParameters() as $parameter) {
            $notifiedWith[] = str_replace('__', '.',
                $parameter->getName());
        }

        return $notifiedWith;
    }

    protected function get_notification_table(): string {
        if (!$this->id) {
            throw new NotImplemented($this);
        }

        return "notifications__{$this->id}";
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function clear(): void {
    }
}