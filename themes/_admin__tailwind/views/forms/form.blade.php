<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var string $route_name */
/* @var int $object_count */
?>
<x-std-pages::layout :title='\Osm\__($form->interface->s_new_object) . " | {$osm_app->http->title}"'>
    <div class="container mx-auto px-4 grid grid-cols-12">
        <section class="col-start-1 col-span-12">
            <form method="POST" action="{{ $form->interface->url('/create') }}"
                data-js-form="{{ json_encode((object)$form->options) }}">

                @if ($route_name === 'GET /create')
                    <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                        {{ \Osm\__($form->interface->s_new_object) }}
                    </h1>
                    <div>
                        <button type="submit"
                            class="text-white bg-blue-700
                                hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                mr-3 mb-3">{{ \Osm\__("Save")}}</button>
                    </div>
                @elseif ($route_name === 'GET /edit')
                    @if ($object_count > 1)
                        <?php throw new \Osm\Core\Exceptions\NotImplemented('1'); ?>
                    @else
                        <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                            <?php throw new \Osm\Core\Exceptions\NotImplemented('2'); ?>
                        </h1>
                        <div>
                            <button type="submit"
                                class="text-white bg-blue-700
                                    hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    mr-3 mb-3">{{ \Osm\__("Save")}}</button>
                        </div>
                    @endif
                @else
                    <?php throw new \Osm\Core\Exceptions\NotSupported(); ?>
                @endif

                @foreach ($form->chapters as $chapter)
                    @include ($chapter->template, ['chapter' => $chapter])
                @endforeach
            </form>
        </section>
    </div>
</x-std-pages::layout>
