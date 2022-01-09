<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var \Osm\Admin\Forms\Chapter $chapter */
/* @var \Osm\Admin\Forms\Section $section */
/* @var \Osm\Admin\Forms\Fieldset $fieldset */
/* @var \Osm\Admin\Forms\Field\Int_ $field */
/* @var string $id */
/* @var \stdClass $object */
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
            @if (property_exists($object, $field->name))
                value="{{ $object->{$field->name} }}"
            @elseif ($route_name === 'GET /edit' && $object_count > 1)
                placeholder="{{ \Osm\__("<multiple values>")}}"
            @endif
        >
    </div>
</div>