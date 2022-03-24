<?php
/* @var string $name */
/* @var string $title */
/* @var string $value */
/* @var bool $multiple */
/* @var array $js */
?>
<div class="field grid grid-cols-12 mb-6"
    data-js-input-field='{!! \Osm\js($js)!!}'
>
    <label for="{{ $name }}"
        class="col-start-1 col-span-12 md:col-start-1 md:col-span-3
            mb-2 md:mb-0 flex items-center
            text-sm font-medium text-gray-900"
    >
        <span>
            {{ $title }}
        </span>
    </label>
    <div class="col-start-1 col-span-12 md:col-start-4 md:col-span-9">
        @include ('ui::form.field.multiple')
        <div class="field__single relative @if ($multiple) hidden @endif">
            <input type="text" name="{{ $name }}" id="{{ $name }}"
                class="field__single-input bg-gray-50 border border-gray-300
                    rounded-lg p-2.5 w-full
                    text-gray-900 sm:text-sm
                    focus:ring-blue-500 focus:border-blue-500"
                    value="{{ $value }}"
            >
            <div class="field__actions flex absolute inset-y-0 right-2 my-1">
                <button class="field__action field__reset-initial-value hidden
                    flex items-center p-2 text-gray-600"
                    title="{{ \Osm\__("Modified. Reset initial value") }}"
                    tabindex="-1" type="button"
                >
                    <i class="fas fa-pencil-alt"></i>
                </button>
            </div>
        </div>
    </div>
</div>