<?php
/* @var \Osm\Admin\Ui\Form $form */
/* @var \Osm\Admin\Schema\Table $table */
/* @var \Osm\Admin\Ui\Result $result */
/* @var string $title */
/* @var string $save_url */
/* @var string $close_url */
/* @var int $count */
/* @var array $js */
?>

@extends('ui::layout')
@section('title', $title)
@section('main')
    <div class="container mx-auto px-4 grid grid-cols-12">
        <section class="col-start-1 col-span-12">
            <form method="POST" action="{{ $save_url }}"
                autocomplete="off"
                data-js-form='{!! \Osm\js($js) !!}'>

                <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                    {{ $title }}
                </h1>
                <div>
                    <a href="{{ $close_url }}"
                        class="text-white bg-blue-700
                            hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                            font-medium rounded-lg text-sm px-5 py-2.5 text-center
                            mr-3 mb-3">{{ \Osm\__("Close")}}</a>
                    <button type="submit"
                        class="text-white bg-blue-700
                            hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                            font-medium rounded-lg text-sm px-5 py-2.5 text-center
                            mr-3 mb-3">{{ \Osm\__("Save")}}</button>
                    @if ($count > 0)
                        <button type="button"
                            class="form__action -delete text-white bg-red-700
                                hover:bg-red-800 focus:ring-4 focus:ring-red-300
                                font-medium rounded-lg text-sm px-5 py-2.5 text-center
                                mr-3 mb-3">{{ \Osm\__("Delete")}}</button>
                    @endif
                </div>

                @foreach ($form->chapters as $chapter)
                    @include ($chapter->template, $chapter->data)
                @endforeach
            </form>
        </section>
    </div>
@endsection
