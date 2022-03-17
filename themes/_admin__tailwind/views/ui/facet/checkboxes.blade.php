<?php
/* @var string $title */
/* @var \Osm\Admin\Ui\Query\Facet\Option[] $options */
?>
<aside class="overflow-hidden shadow-md sm:rounded-lg my-4 mr-8">
    <h2 class="text-xl px-4 py-2 bg-gray-50 dark:bg-gray-700">{{ $title }}</h2>
    <ul class="text-sm px-4 py-2 text-gray-500 dark:text-gray-400">
        @foreach ($options as $option)
            <li class="p-2 my -mx-2" data-js-facet-checkbox>
                <a class="block pl-6 relative" href="{{ $option->url }}"
                    title="{{ $option->title }} ({{ $option->count }})"
                >
                    <span class="absolute left-0 top-0.5">
                        <input type="checkbox"
                               class="w-4 h-4 bg-gray-50 rounded border
                                border-gray-300 focus:ring-3
                                focus:ring-blue-300 dark:bg-gray-700
                                dark:border-gray-600 dark:focus:ring-blue-600
                                dark:ring-offset-gray-800 cursor-pointer"
                                @if ($option->applied) checked @endif
                            >
                    </span>
                    <span>{{ $option->title }} ({{ $option->count }})</span>
                </a>
            </li>
        @endforeach
    </ul>
</aside>