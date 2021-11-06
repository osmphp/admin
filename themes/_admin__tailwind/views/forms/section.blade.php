<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Section\Standard $section */
?>
@foreach ($section->groups as $group)
    @include ($group->template, ['group' => $group])
@endforeach