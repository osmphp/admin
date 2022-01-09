<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var \Osm\Admin\Forms\Chapter $chapter */
/* @var \Osm\Admin\Forms\Section $section */
/* @var \Osm\Admin\Forms\Fieldset $fieldset */
/* @var array $field_options */
?>
<fieldset>
    @foreach ($fieldset->fields as $field)
        @include ($field->template, [
            'field' => $field,
            'options' => $field_options[$field->name],
        ])
    @endforeach
</fieldset>
