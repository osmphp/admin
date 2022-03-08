<?php
/* @var \Osm\Admin\Ui\Facet[] $facets */
?>
@foreach($facets as $facet)
    @if ($facet->visible)
        @include($facet->template, $facet->data)
    @endif
@endforeach
