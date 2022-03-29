<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */

/* @var \Osm\Admin\Ui\List_\Grid $grid */
/* @var \Osm\Admin\Schema\Table $table */
/* @var \Osm\Admin\Ui\Result $result */
/* @var string $title */
/* @var string $create_url */
/* @var array $js */
?>

@extends('ui::layout')
@section('title', $title)
@section('main')
    <div class="grid_" data-js-grid='{!! \Osm\js($js) !!}'>
        <section>
            <h1 class="text-2xl sm:text-4xl my-6">
                {{ $title }}
            </h1>
            <p class="text-sm mb-6">
                <span class="grid__selected">
                    {{ \Osm\__($table->s_n_m_objects_selected, [
                        'selected' => 0,
                        'count' => $result->count,
                    ]) }}
                </span>
            </p>
            <div class="my-4">
                <a href="{{ $create_url }}"
                    class="text-white bg-blue-700
                        hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                        font-medium rounded-lg text-sm px-5 py-2.5 text-center
                        mr-3 mb-3">{{ \Osm\__("Create")}}</a>
                <a href="#"
                    class="grid__action -edit hidden text-white bg-blue-700
                        hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                        font-medium rounded-lg text-sm px-5 py-2.5 text-center
                        mr-3 mb-3">{{ \Osm\__("Edit")}}</a>
                <button type="button"
                    class="grid__action -delete hidden text-white bg-red-700
                        hover:bg-red-800 focus:ring-4 focus:ring-red-300
                        font-medium rounded-lg text-sm px-5 py-2.5 text-center
                        mr-3 mb-3">{{ \Osm\__("Delete")}}</button>
            </div>
        </section>
        <section class="overflow-hidden shadow-md sm:rounded-lg">
            <div class="table min-w-full border-collapse">
                <div class="table-header-group bg-gray-50 dark:bg-gray-700">
                    <div class="table-row">
                        @include('ui::grid.header.handle')
                        @foreach ($grid->columns as $column)
                            @include($column->template, $column->data)
                        @endforeach
                    </div>
                </div>
                <div class="table-row-group">
                    @forelse($result->items as $item)
                        <div class="grid__row table-row bg-white border-b
                            dark:bg-gray-800 dark:border-gray-700"
                            data-js-grid-row='{"id": {{ $item->id }}}'
                        >
                            @include ('ui::grid.cell.handle')
                            @foreach ($grid->columns as $column)
                                @include($column->cell_template,
                                    $column->data($item))
                            @endforeach
                        </div>
                    @empty
                        <div class="table-row bg-white border-b relative
                            dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div class="table-cell py-3 px-6 text-xs
                                tracking-wider absolute left-0 right-0
                                text-center h-20 top-1/2 [transform:translateY(-25%)]"
                            >
                                {{ \Osm\__($table->s_no_objects) }}
                            </div>
                            @foreach ($grid->columns as $column)
                                <div class="table-cell py-3 px-6 text-xs
                                    tracking-wider h-20"></div>
                            @endforeach
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
