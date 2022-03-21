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
        <div class="relative">
            <input type="text" name="{{ $name }}" id="{{ $name }}"
                class="bg-gray-50 border border-gray-300 rounded-lg p-2.5 w-full
                    text-gray-900 sm:text-sm
                    focus:ring-blue-500 focus:border-blue-500"
                    value="{{ $value }}"
                @if ($multiple)
                    placeholder="{{ \Osm\__("<multiple values>")}}"
                @endif
            >
            <div class="field__actions flex absolute inset-y-0 right-2 my-1">
                @if ($multiple)
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