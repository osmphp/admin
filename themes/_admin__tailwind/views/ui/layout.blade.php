<?php
/* @var \Osm\Admin\Ui\Sidebar $sidebar */
?>
@extends('std-pages::layout')
@section('content')
    @if(!empty($sidebar) && $sidebar->visible)
        <div class="container mx-auto px-4 grid grid-cols-12">
            <div class="col-start-1 col-span-3">
                @include($sidebar->template, $sidebar->data)
            </div>
            <div class="col-start-4 col-span-9">
                @yield('main')
            </div>
        </div>
    @else
        <div class="container mx-auto px-4">
            @yield('main')
        </div>
    @endif

@endsection
