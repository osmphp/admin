<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Grids\Grid $grid */
?>
<x-std-pages::layout :title='\Osm\__($grid->title) . " | {$osm_app->http->title}"'>
    <div class="container mx-auto px-4 grid grid-cols-12">
        <section class="col-start-1 col-span-12">
            <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                {{ \Osm\__($grid->title) }}
            </h1>
            <div>
                @if ($grid->can_create)
                    <a class="py-2 px-4 rounded bg-gray-700 hover:bg-black text-white"
                        href="{{ $grid->create_url }}" title="Create"
                    >Create</a>
                @endif
            </div>
        </section>
        <section class="col-start-1 col-span-12">
            <x-grids::grid :grid="$grid"/>
        </section>
    </div>
</x-std-pages::layout>