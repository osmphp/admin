<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form\Create $form */
?>
<x-std-pages::layout :title='\Osm\__($form->title) . " | {$osm_app->http->title}"'>
    <div class="container mx-auto px-4 grid grid-cols-12">
        <section class="col-start-1 col-span-12">
            @include ($form->template, ['form' => $form])
        </section>
    </div>
</x-std-pages::layout>