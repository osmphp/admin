<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
?>
<div class="flex">
    @foreach ($osm_app->schema->icons as $icon)
        @include($icon->template, ['icon' => $icon])
    @endforeach
</div>