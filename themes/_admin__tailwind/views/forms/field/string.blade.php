<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Field\String_ $field */
?>
<div class="field grid grid-cols-12" data-js-string-field>
    <label for="{{ "{$id}{$field->name}" }}"
        class="col-start-1 col-span-12 md:col-start-1 md:col-span-3"
    >
        {{ $field->title }}
    </label>
    <div class="col-start-1 col-span-12 md:col-start-4 md:col-span-9">
        <input type="text" name="{{ $field->name }}"
            id="{{ "{$id}{$field->name}" }}"
            class="border border-gray-300 w-full p-2"
        >
    </div>
</div>