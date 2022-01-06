<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var \Osm\Admin\Forms\Chapter $chapter */
/* @var \Osm\Admin\Forms\Section $section */
?>
@foreach ($section->fieldsets as $fieldset)
    @include ($fieldset->template, ['fieldset' => $fieldset])
@endforeach