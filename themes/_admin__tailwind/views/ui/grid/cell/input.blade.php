<?php
/* @var \Osm\Admin\Ui\List_\Grid $grid */
/* @var \Osm\Admin\Ui\Control $column */
/* @var \stdClass $item */
?>
@if (true)
    <a href="{{ $grid->editUrl($item) }}"
        class="table-cell py-4 px-6 text-sm
            text-gray-500 whitespace-nowrap
            dark:text-gray-400 text-left"
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
