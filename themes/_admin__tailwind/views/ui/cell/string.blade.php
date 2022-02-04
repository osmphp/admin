<?php
/* @var \Osm\Admin\Ui\Interface_ $interface */
/* @var callable $editUrl */
/* @var \Osm\Admin\Grids\Column\String_ $column */
/* @var \stdClass $object */
?>
@if ($column->edit_link)
    <a href="{{ $editUrl($object) }}"
        class="table-cell py-4 px-6 text-sm
            text-gray-500 whitespace-nowrap
            dark:text-gray-400 text-left underline"
    >
        {{ $object->{$column->name} ?? null }}
    </a>
@else
    <div class="table-cell py-4 px-6 text-sm
        text-gray-500 whitespace-nowrap
        dark:text-gray-400 text-left"
    >
        {{ $object->{$column->name} ?? null }}
    </div>
@endif
