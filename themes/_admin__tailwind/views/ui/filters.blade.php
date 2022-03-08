<?php
/* @var \Osm\Admin\Ui\Filter[] $filters */
?>
@foreach($filters as $filter)
    @if ($filter->visible)
        @include($filter->template, $filter->data)
    @endif
@endforeach
