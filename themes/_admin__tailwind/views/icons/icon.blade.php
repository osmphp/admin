<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Icons\Icon $icon */
?>
<a href="{{ "{$osm_app->area_url}{$icon->url}"}}" title="{{ $icon->title}}">
    <div class="text-center p-4">
        <div class="text-3xl">
            <i class="fas fa-table"></i>
        </div>
        <div>{{ $icon->title }}</div>
    </div>
</a>