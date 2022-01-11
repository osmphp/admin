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
    <div class="col-start-1 col-span-12 md:col-start-4 md:col-span-9">
        <div class="relative">
            <input type="text" name="{{ $field->name }}" id="{{ $field->name }}"
                class="bg-gray-50 border border-gray-300 rounded-lg p-2.5 w-full
                    text-gray-900 sm:text-sm
                    focus:ring-blue-500 focus:border-blue-500"
                    value="{{ $options['value'] }}"
                @if ($options['multiple'])
                    placeholder="{{ \Osm\__("<multiple values>")}}"
                @endif
            >
            <div class="field__actions flex absolute inset-y-0 right-2 my-1">
                @if ($options['multiple'])
                    <button class="field__action field__clear flex items-center p-2 text-gray-600"
                        title="{{ \Osm\__("Clear all values") }}"
                        tabindex="-1" type="button"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </button>
                @endif
                <button class="field__action field__reset hidden flex items-center p-2 text-gray-600"
                    title="{{ \Osm\__("Modified. Reset to initial value") }}"
                    tabindex="-1" type="button"
                >
                    <i class="fas fa-pencil-alt"></i>
                </button>
            </div>
        </div>
    </div>
</div>