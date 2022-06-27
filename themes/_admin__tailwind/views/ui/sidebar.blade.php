<?php
/* @var \Osm\Admin\Ui\Menu $menu */
/* @var \Osm\Admin\Ui\Facets $facets */
?>

@isset($menu?->visible)
    @include($menu->template, $menu->data)
@endif

@isset($facets?->visible)
    @include($facets->template, $facets->data)
@endif
