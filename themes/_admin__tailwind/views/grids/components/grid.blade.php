<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Grids\Grid $grid */
?>
<div class="table">
    <div class="table-header-group">
        <div class="table-row">
            @foreach ($grid->selected_columns as $column)
                <div class="table-cell p-4">
                    {{ \Osm\__($column->title) }}
                </div>
            @endforeach
        </div>
    </div>
    <div class="table-row-group">

    </div>
</div>