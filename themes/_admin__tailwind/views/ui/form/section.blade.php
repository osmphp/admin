<?php
/* @var \Osm\Admin\Ui\Form\Section $section */
?>
@foreach ($section->fieldsets as $fieldset)
    @include ($fieldset->template, $fieldset->data)
@endforeach