<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Field\Int_ $field */
?>
<div class="field grid grid-cols-12 mb-6" data-js-int-field>
    <label for="{{ "{$id}{$field->name}" }}"
        class="col-start-1 col-span-12 md:col-start-1 md:col-span-3
            mb-2 md:mb-0 flex items-center
            text-sm font-medium text-gray-900"
    >
        <span>
            {{ $field->title }}
        </span>
    </label>
    <div class="col-start-1 col-span-12 md:col-start-4 md:col-span-9">
        <input type="text" name="{{ $field->name }}" id="{{ "{$id}{$field->name}" }}"
            class="bg-gray-50 border border-gray-300 rounded-lg p-2.5 w-40
                text-gray-900 sm:text-sm
                focus:ring-blue-500 focus:border-blue-500"
        >
    </div>
</div>