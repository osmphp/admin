<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form $form */
/* @var string $route_name */
/* @var int $object_id */
/* @var int $object_count */
/* @var string $title */
/* @var \stdClass $object */
/* @var string $form_url */
/* @var array $options */
/* @var array $field_options */
?>
<x-std-pages::layout :title='"{$title} | {$osm_app->http->title}"'>
    <div class="container mx-auto px-4 grid grid-cols-12">
        <section class="col-start-1 col-span-12">
            <form method="POST" action="{{ $form_url }}"
                autocomplete="off"
                data-js-form='{!! \Osm\js($options) !!}'>

                @if ($route_name === 'GET /create')
                    <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                        {{ $title }}
                    </h1>
                    <div>
                        <a href="{{ $grid_url }}"
                            class="text-white bg-blue-700
                                hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                mr-3 mb-3">{{ \Osm\__("Close")}}</a>
                        <button type="submit"
                            class="text-white bg-blue-700
                                hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                mr-3 mb-3">{{ \Osm\__("Save")}}</button>
                    </div>
                @elseif ($route_name === 'GET /edit')
                    @if ($object_count > 1)
                        <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                            {{ $title }}
                        </h1>
                        <div>
                            <a href="{{ $grid_url }}"
                                class="text-white bg-blue-700
                                    hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    mr-3 mb-3">{{ \Osm\__("Close")}}</a>
                            <button type="submit"
                                class="text-white bg-blue-700
                                    hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    mr-3 mb-3">{{ \Osm\__("Save")}}</button>
                            <button type="button"
                                class="form__action -delete text-white bg-red-700
                                    hover:bg-red-800 focus:ring-4 focus:ring-red-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    mr-3 mb-3">{{ \Osm\__("Delete")}}</button>
                        </div>
                    @else
                        <h1 class="text-2xl sm:text-4xl pt-6 border-t border-gray-300">
                            {{ $title }}
                        </h1>
                        <p class="text-sm mb-6">
                            @if (isset($object->title))
                                {{ \Osm\__($form->interface->s_object_id, [
                                    'id' => $object->id,
                                ]) }}
                            @endif
                        </p>
                        <div>
                            <a href="{{ $grid_url }}"
                                class="text-white bg-blue-700
                                    hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    mr-3 mb-3">{{ \Osm\__("Close")}}</a>
                            <button type="submit"
                                class="text-white bg-blue-700
                                    hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    mr-3 mb-3">{{ \Osm\__("Save")}}</button>
                            <button type="button"
                                class="form__action -delete text-white bg-red-700
                                    hover:bg-red-800 focus:ring-4 focus:ring-red-300
                                    font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                    mr-3 mb-3">{{ \Osm\__("Delete")}}</button>
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
