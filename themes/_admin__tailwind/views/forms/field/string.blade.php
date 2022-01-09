<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var \Osm\Admin\Forms\Chapter $chapter */
/* @var \Osm\Admin\Forms\Section $section */
/* @var \Osm\Admin\Forms\Fieldset $fieldset */
/* @var \Osm\Admin\Forms\Field\String_ $field */
/* @var string $route_name */
/* @var \stdClass $object */
/* @var array $options */
?>
<div class="field grid grid-cols-12 mb-6"
    data-js-string-field='{!! \Osm\js($options)!!}'
>
    <label for="{{ $field->name }}"
        class="col-start-1 col-span-12 md:col-start-1 md:col-span-3
            mb-2 md:mb-0 flex items-center
            text-sm font-medium text-gray-900"
    >
        <span>
            {{ $field->title }}
        </span>
    </label>
    <div class="col-start-1 col-span-12 md:col-start-4 md:col-span-9 relative">
        <input type="text" name="{{ $field->name }}" id="{{ $field->name }}"
            class="bg-gray-50 border border-gray-300 rounded-lg p-2.5 w-full
                text-gray-900 sm:text-sm
                focus:ring-blue-500 focus:border-blue-500"
            @if ($options['initial_value_exists'])
                value="{{ $options['initial_value'] }}"
            @elseif ($route_name === 'GET /edit' && $object_count > 1)
                placeholder="{{ \Osm\__("<multiple values>")}}"
            @endif
        >
        <button class="flex absolute inset-y-0 right-2 items-center my-1 p-2 text-gray-600">
            <i class="fas fa-pencil-alt"></i>
        </button>
    </div>
</div>