<?php
/* @var callable $editUrl */
/* @var \Osm\Admin\Ui\Column $column */
/* @var \stdClass $item */
?>
@if ($column->edit_link)
    <a href="{{ $editUrl($item) }}"
        class="table-cell py-4 px-6 text-sm
            text-gray-500 whitespace-nowrap
            dark:text-gray-400 text-left underline"
    >
        {{ $item->{$column->name} ?? null }}
    </a>
@else
    <div class="table-cell py-4 px-6 text-sm
        text-gray-500 whitespace-nowrap
        dark:text-gray-400 text-left"
    >
        {{ $item->{$column->name} ?? null }}
    </div>
@endif
