<?php
/* @var bool $multiple */
?>
@if ($multiple)
    <div class="field__multiple relative">
        <input class="field__multiple-input bg-gray-50 border
            border-gray-300 rounded-lg p-2.5 w-full
            text-gray-900 sm:text-sm
            focus:ring-blue-500 focus:border-blue-500"
            type="text"
            value="{{ \Osm\__("<multiple values>") }}"
            readonly
        >
        <div class="flex absolute inset-y-0 right-2 my-1">
            <button class="field__clear-multiple-values
                flex items-center p-2 text-gray-600"
                title="{{ \Osm\__("Clear all values") }}"
                tabindex="-1" type="button"
            >
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>
@endif
