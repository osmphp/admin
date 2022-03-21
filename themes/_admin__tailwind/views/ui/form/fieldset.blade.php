<?php
/* @var \Osm\Admin\Ui\Form\Fieldset $field */
?>
<fieldset>
    @foreach ($fieldset->fields as $field)
        @include ($field->template, $field->data)
    @endforeach
</fieldset>
