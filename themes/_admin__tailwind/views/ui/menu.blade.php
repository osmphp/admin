<?php
/* @var \stdClass[]|\Osm\Admin\Ui\MenuItem[] $items */
?>

<aside class="overflow-hidden shadow-md sm:rounded-lg my-4 mr-8">
    <h2 class="text-xl px-4 py-2 bg-gray-50 dark:bg-gray-700">{{ \Osm\__("Menu") }}</h2>
    <ul class="text-sm px-4 py-2 text-gray-500 dark:text-gray-400">
        @foreach ($items as $item)
            <li class="p-2 my -mx-2">
                <a class="block" href="{{ $item->url }}"
                    title="{{ $item->title }}"
                >
                    <span>{{ $item->title }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</aside>