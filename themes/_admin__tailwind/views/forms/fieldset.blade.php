<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var \Osm\Admin\Forms\Chapter $chapter */
/* @var \Osm\Admin\Forms\Section $section */
/* @var \Osm\Admin\Forms\Fieldset $fieldset */
?>
<fieldset>
    @foreach ($fieldset->fields as $field)
        @include ($field->template, ['field' => $field, 'id' => ''])
    @endforeach
</fieldset>
