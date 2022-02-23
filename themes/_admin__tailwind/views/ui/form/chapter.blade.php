<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var \Osm\Admin\Forms\Chapter $chapter */
?>
@foreach ($chapter->sections as $section)
    @include ($section->template, ['section' => $section])
@endforeach