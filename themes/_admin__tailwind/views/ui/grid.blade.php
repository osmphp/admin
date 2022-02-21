<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */

/* @var string $title */
/* @var array $js */
/* @var \Osm\Admin\Schema\Table $table */
/* @var int $count */
/* @var string $create_url */
/* @var \Osm\Admin\Ui\Grid $grid */
/* @var \stdClass[] $items */

/* @var string $route_name */
/* @var string $edit_url */
/* @var callable $editUrl */
?>
<x-std-pages::layout :title='"{$title} | {$osm_app->http->title}"'>
    <div class="container mx-auto px-4 grid grid-cols-12">
        <div class="grid_ col-start-1 col-span-12"
            data-js-grid='{!! \Osm\js($js) !!}'
        >
            <section>
                <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
                    {{ $title }}
                </h1>
                <p class="text-sm mb-6">
                    <span class="grid__selected">
                        {{ \Osm\__($table->s_n_m_objects_selected, [
                            'selected' => 0,
                            'count' => $count,
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
                                @if ($column->header_template)
                                    @include($column->header_template, [
                                        'column' => $column,
                                    ])
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="table-row-group">
                        @forelse($items as $item)
                            <div class="grid__row table-row bg-white border-b
                                dark:bg-gray-800 dark:border-gray-700"
                                data-js-row='{"id": {{ $item->id }}}'
                            >
                                @include ('ui::grid.cell.handle')
                                @foreach ($grid->columns as $column)
                                    @if ($column->cell_template)
                                        @include($column->cell_template, [
                                            'column' => $column,
                                            'item' => $item,
                                        ])
                                    @endif
                                @endforeach
                            </div>
                        @empty
                            {{ \Osm\__($table->s_no_objects) }}
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-std-pages::layout>
