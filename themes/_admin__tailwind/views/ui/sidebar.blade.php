<?php
/* @var \Osm\Admin\Ui\Facets $facets */
?>
@isset($facets?->visible)
    @include($facets->template, $facets->data)
@endif
