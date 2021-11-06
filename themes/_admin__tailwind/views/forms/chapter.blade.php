<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Chapter\Standard $chapter */
?>
@foreach ($chapter->sections as $section)
    @include ($section->template, ['section' => $section])
@endforeach