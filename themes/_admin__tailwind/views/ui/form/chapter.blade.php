<?php
/* @var \Osm\Admin\Ui\Form\Chapter $chapter */
?>
@foreach ($chapter->sections as $section)
    @include ($section->template, $section->data)
@endforeach