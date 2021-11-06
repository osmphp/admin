<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
?>
<x-std-pages::layout>
    <div class="container mx-auto px-4 grid grid-cols-12">
        <section class="col-start-1 col-span-12">
            <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                {{ $osm_app->http->title }}
            </h1>
        </section>
        <section class="col-start-1 col-span-12">
            @include('icons::all')
        </section>
    </div>
</x-std-pages::layout>