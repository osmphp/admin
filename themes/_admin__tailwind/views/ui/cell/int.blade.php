<?php
/* @var \Osm\Admin\Ui\Interface_ $interface */
/* @var callable $editUrl */
/* @var \Osm\Admin\Grids\Column\Int_ $column */
/* @var \stdClass $object */
?>
@if ($column->edit_link)
    <a href="{{ $editUrl($object) }}"
        class="table-cell py-4 px-6 text-sm
            text-gray-500 whitespace-nowrap
            dark:text-gray-400 text-center underline"
    >
        {{ $object->{$column->name} ?? null }}
    </a>
@else
    <div class="table-cell py-4 px-6 text-sm
        text-gray-500 whitespace-nowrap
        dark:text-gray-400 text-center"
    >
        {{ $object->{$column->name} ?? null }}
    </div>
@endif
