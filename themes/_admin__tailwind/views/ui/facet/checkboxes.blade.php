<?php
/* @var string $title */
/* @var \Osm\Admin\Ui\Query\Facet\Option[] $options */
?>
<h2 class="text-xl mt-8 mb-4">{{ $title }}</h2>
<ul>
    @foreach ($options as $option)
        <li class="p-2 my-2 -mx-2">
            <a class="block pl-6 relative" href="{{ $option->url }}"
                title="{{ $option->title }} ({{ $option->count }})"
            >
                <span class="absolute left-0">
                    <input type="checkbox"
                           class="w-4 h-4 bg-gray-50 rounded border
                            border-gray-300 focus:ring-3
                            focus:ring-blue-300 dark:bg-gray-700
                            dark:border-gray-600 dark:focus:ring-blue-600
                            dark:ring-offset-gray-800 cursor-pointer">
                </span>
                <span>{{ $option->title }} ({{ $option->count }})</span>
            </a>
        </li>
    @endforeach
</ul>
