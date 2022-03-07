<?php
/* @var \Osm\Admin\Ui\Filters $filters */
?>
@isset($filters?->visible)
    @include($filters->template, $filters->data)
@endif
