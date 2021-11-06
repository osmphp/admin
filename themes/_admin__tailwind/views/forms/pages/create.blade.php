<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form\Create $form */
?>
<x-std-pages::layout :title='\Osm\__($form->title) . " | {$osm_app->http->title}"'>
    <div class="container mx-auto px-4 grid grid-cols-12">
        <section class="col-start-1 col-span-12">
            <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                {{ \Osm\__($form->title) }}
            </h1>
        </section>
        <section class="col-start-1 col-span-12">
            <form method="POST" action="{{ $form->save_url}}">
                @include ($form->template, ['form' => $form])
            </form>
        </section>
    </div>
</x-std-pages::layout>