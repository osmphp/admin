<?php
/* @var string $name */
/* @var string $title */
/* @var string $value */
/* @var \Osm\Admin\Schema\Option[] $options */
/* @var bool $multiple */
/* @var array $js */
?>
<div class="field grid grid-cols-12 mb-6"
    data-js-select-field='{!! \Osm\js($js)!!}'
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
            <select name="{{ $name }}" id="{{ $name }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm
                    rounded-lg focus:ring-blue-500 focus:border-blue-500
                    block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                    dark:placeholder-gray-400 dark:text-white
                    dark:focus:ring-blue-500 dark:focus:border-blue-500
                    appearance-none"
            >
                <option value="" @if ($value === null) selected @endif></option>
                @foreach ($options as $option)
                    <option value="{{ $option->value}}"
                        @if ($value === $option->value) selected @endif
                    >{{ $option->title }}</option>
                @endforeach
            </select>
            <div class="field__actions flex absolute inset-y-0 right-2 my-1
                pointer-events-none">
                <button class="flex items-center p-2 text-gray-600"
                    tabindex="-1" type="button"
                >
                    <i class="fas fa-angle-down"></i>
                </button>
                <button class="field__action field__reset-initial-value hidden
                    flex items-center p-2 text-gray-600 pointer-events-auto"
                    title="{{ \Osm\__("Modified. Reset to initial value") }}"
                    tabindex="-1" type="button"
                >
                    <i class="fas fa-pencil-alt"></i>
                </button>
            </div>
        </div>
    </div>
</div>