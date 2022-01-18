<?php
/* @var \Osm\Admin\Grids\Column $column */
/* @var \stdClass $object */
?>
<div class="table-cell py-4 px-6 text-sm
    text-gray-500 whitespace-nowrap
    dark:text-gray-400"
>
    {{ $object->{$column->name} ?? null }}
</div>
