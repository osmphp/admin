<?php

namespace Osm\Admin\Ui;

use Osm\Core\Exceptions\NotImplemented;
use function Osm\__;
use function Osm\ui_query;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Blade\Attributes\RenderTime;

/**
 * @property array $layout #[Serialized]
 * @property Form\Chapter[] $chapters #[Serialized]
 * @property int $count #[RenderTime]
 * @property \stdClass $item #[RenderTime]
 * @property string $title #[RenderTime]
 * @property string $save_url #[RenderTime]
 *
 * @uses Serialized, RenderTime
 */
class Form extends ObjectView
{
    public string $template = 'ui::form';

    protected function get_query(): Query {
        $query = ui_query($this->table->name)
            ->fromUrl($this->http_query,
                'limit', 'offset', 'order', 'select')
            ->count();

        $query->db_query->select('id', 'title');

        foreach ($this->chapters as $chapter) {
            $chapter->prepare($query);
        }

        return $query;
    }

    protected function get_count(): int {
        return $this->result->count;
    }

    protected function get_item(): \stdClass {
        if ($this->count === 0) {
            return new \stdClass();
        }

        if ($this->count === 1) {
            return $this->result->items[0];
        }

        throw new NotImplemented($this);
    }

    protected function get_title(): string {
        if ($this->count === 0) {
            return __($this->struct->s_new_object);
        }

        if ($this->count === 1) {
            return $this->item->title;
        }

        throw new NotImplemented($this);
    }

    protected function get_save_url(): string {
        if ($this->count === 0) {
            return $this->table->url('POST /create');
        }

        return $this->query->toUrl('POST /');
    }

    protected function get_data(): array {
        return [
            'form' => $this,
            'table' => $this->table,
            'result' => $this->result,
            'title' => $this->title,
            'save_url' => $this->save_url,
            'close_url' => $this->query->toUrl('GET /'),
            'count' => $this->count,
            'js' => [

            ],
        ];
    }

    protected function get_layout(): array {
        return [
            // default chapter
            '' => [
                'layout' => [
                    // default section
                    '' => [
                        'layout' => [
                            // default fieldset
                            '' => [
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function get_chapters(): array {
        $chapters = [];

        foreach ($this->layout as $name => $data) {
            $data['name'] = $name;
            $data['form'] = $this;
            $chapters[$name] = Form\Chapter::new($data);
        }

        return $chapters;
    }

    public function __wakeup(): void
    {
        foreach ($this->chapters as $chapter) {
            $chapter->form = $this;
        }
    }
}